<?php

namespace Native\Laravel\Commands;

use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Native\Laravel\Commands\Traits\CleansEnvFile;
use Native\Laravel\Commands\Traits\HandleApiRequests;
use Symfony\Component\Finder\Finder;
use ZipArchive;

class BundleCommand extends Command
{
    use CleansEnvFile, HandleApiRequests;

    protected $signature = 'native:bundle {--fetch} {--without-cleanup}';

    protected $description = 'Bundle your application for distribution.';

    private ?string $key;

    private string $zipPath;

    private string $zipName;

    public function handle(): int
    {
        // Check for ZEPHPYR_KEY
        if (! $this->checkForZephpyrKey()) {
            return static::FAILURE;
        }

        // Check for ZEPHPYR_TOKEN
        if (! $this->checkForZephpyrToken()) {
            return static::FAILURE;
        }

        // Check if the token is valid
        if (! $this->checkAuthenticated()) {
            $this->error('Invalid API token: check your ZEPHPYR_TOKEN on '.$this->baseUrl().'user/api-tokens');

            return static::FAILURE;
        }

        // Download the latest bundle if requested
        if ($this->option('fetch')) {
            if (! $this->fetchLatestBundle()) {

                return static::FAILURE;
            }

            $this->info('Latest bundle downloaded.');

            return static::SUCCESS;
        }

        // Check composer.json for symlinked or private packages
        if (! $this->checkComposerJson()) {
            return static::FAILURE;
        }

        // Package the app up into a zip
        if (! $this->zipApplication()) {
            $this->error("Failed to create zip archive at {$this->zipPath}.");

            return static::FAILURE;
        }

        // Send the zip file
        $result = $this->sendToZephpyr();
        $this->handleApiErrors($result);

        // Success
        $this->info('Successfully uploaded to Zephpyr.');
        $this->line('Use native:bundle --fetch to retrieve the latest bundle.');

        // Clean up temp files
        $this->cleanUp();

        return static::SUCCESS;
    }

    private function zipApplication(): bool
    {
        $this->zipName = 'app_'.str()->random(8).'.zip';
        $this->zipPath = base_path('temp/'.$this->zipName);

        // Create zip path
        if (! @mkdir(dirname($this->zipPath), recursive: true) && ! is_dir(dirname($this->zipPath))) {
            return false;
        }

        $zip = new ZipArchive;

        if ($zip->open($this->zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        $this->prepareNativeEnv();

        $this->addFilesToZip($zip);

        $zip->close();

        $this->restoreWebEnv();

        return true;
    }

    private function checkComposerJson(): bool
    {
        $composerJson = json_decode(file_get_contents(base_path('composer.json')), true);

        // Fail if there is symlinked packages
        foreach ($composerJson['repositories'] ?? [] as $repository) {
            if ($repository['type'] === 'path') {
                $this->error('Symlinked packages are not supported. Please remove them from your composer.json.');

                return false;
            }
            // elseif ($repository['type'] === 'composer') {
            //     if (! $this->checkComposerPackageAuth($repository['url'])) {
            //         $this->error('Cannot authenticate with '.$repository['url'].'.');
            //         $this->error('Go to '.$this->baseUrl().' and add your composer package credentials.');
            //
            //         return false;
            //     }
            // }
        }

        return true;
    }

    // private function checkComposerPackageAuth(string $repositoryUrl): bool
    // {
    //     $host = parse_url($repositoryUrl, PHP_URL_HOST);
    //     $this->line('Checking '.$host.' authentication…');
    //
    //     return Http::acceptJson()
    //         ->withToken(config('nativephp-internal.zephpyr.token'))
    //         ->get($this->baseUrl().'api/v1/project/'.$this->key.'/composer/auth/'.$host)
    //         ->successful();
    // }

    private function addFilesToZip(ZipArchive $zip): void
    {
        $this->line('Creating zip archive…');

        $app = (new Finder)->files()
            ->followLinks()
            ->ignoreVCSIgnored(true)
            ->in(base_path())
            ->exclude([
                'vendor', // We add this later
                'node_modules', // We add this later
                'dist', // Compiled nativephp assets
                'build', // Compiled box assets
                'temp', // Temp files
                'tests', // Tests
                ...config('nativephp.cleanup_exclude_files', []), // User defined
            ]);

        $this->finderToZip($app, $zip);

        // Add .env file manually because Finder ignores hidden files
        $zip->addFile(base_path('.env'), '.env');

        // Add auth.json file to support private packages
        $zip->addFile(base_path('auth.json'), 'auth.json');

        // Custom binaries
        $binaryPath = Str::replaceStart(base_path('vendor'), '', config('nativephp.binary_path'));

        // Add composer dependencies without unnecessary files
        $vendor = (new Finder)->files()
            ->exclude(array_filter([
                'nativephp/php-bin',
                'nativephp/electron/resources/js',
                'nativephp/*/vendor',
                $binaryPath,
            ]))
            ->in(base_path('vendor'));

        $this->finderToZip($vendor, $zip, 'vendor');

        // Add javascript dependencies
        if (file_exists(base_path('node_modules'))) {
            $nodeModules = (new Finder)->files()
                ->in(base_path('node_modules'));

            $this->finderToZip($nodeModules, $zip, 'node_modules');
        }
    }

    private function finderToZip(Finder $finder, ZipArchive $zip, ?string $path = null): void
    {
        foreach ($finder as $file) {
            if ($file->getRealPath() === false) {
                continue;
            }

            $zip->addFile($file->getRealPath(), str($path)->finish(DIRECTORY_SEPARATOR).$file->getRelativePathname());
        }
    }

    private function sendToZephpyr()
    {
        $this->line('Uploading zip to Zephpyr…');

        return Http::acceptJson()
            ->timeout(300) // 5 minutes
            ->withoutRedirecting() // Upload won't work if we follow redirects (it transform POST to GET)
            ->withToken(config('nativephp-internal.zephpyr.token'))
            ->attach('archive', fopen($this->zipPath, 'r'), $this->zipName)
            ->post($this->baseUrl().'api/v1/project/'.$this->key.'/build/');
    }

    private function fetchLatestBundle(): bool
    {
        $this->line('Fetching latest bundle…');

        $response = Http::acceptJson()
            ->withToken(config('nativephp-internal.zephpyr.token'))
            ->get($this->baseUrl().'api/v1/project/'.$this->key.'/build/download');

        if ($response->failed()) {

            if ($response->status() === 404) {
                $this->error('Project or bundle not found.');
            } elseif ($response->status() === 500) {
                $this->error('Build failed. Please try again later.');
            } elseif ($response->status() === 503) {
                $this->warn('Bundle not ready. Please try again in '.now()->addSeconds(intval($response->header('Retry-After')))->diffForHumans(syntax: CarbonInterface::DIFF_ABSOLUTE).'.');
            } else {
                $this->handleApiErrors($response);
            }

            return false;
        }

        // Save the bundle
        @mkdir(base_path('build'), recursive: true);
        file_put_contents(base_path('build/__nativephp_app_bundle'), $response->body());

        return true;
    }

    protected function exitWithMessage(string $message): void
    {
        $this->error($message);
        $this->cleanUp();

        exit(static::FAILURE);
    }

    private function handleApiErrors(Response $result): void
    {
        if ($result->status() === 413) {
            $fileSize = Number::fileSize(filesize($this->zipPath));
            $this->exitWithMessage('File is too large to upload ('.$fileSize.'). Please contact support.');
        } elseif ($result->status() === 422) {
            $this->error('Request refused:'.$result->json('message'));
        } elseif ($result->status() === 429) {
            $this->exitWithMessage('Too many requests. Please try again in '.now()->addSeconds(intval($result->header('Retry-After')))->diffForHumans(syntax: CarbonInterface::DIFF_ABSOLUTE).'.');
        } elseif ($result->failed()) {
            $this->exitWithMessage("Request failed. Error code: {$result->status()}");
        }
    }

    protected function cleanUp(): void
    {
        if ($this->option('without-cleanup')) {
            return;
        }

        $previousBuilds = glob(base_path('temp/app_*.zip'));
        $failedZips = glob(base_path('temp/app_*.part'));

        $deleteFiles = array_merge($previousBuilds, $failedZips);

        if (empty($deleteFiles)) {
            return;
        }

        $this->line('Cleaning up…');

        foreach ($deleteFiles as $file) {
            @unlink($file);
        }
    }
}
