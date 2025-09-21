<?php

namespace Native\Electron\Commands;

use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Native\Electron\Traits\CleansEnvFile;
use Native\Electron\Traits\CopiesToBuildDirectory;
use Native\Electron\Traits\HandlesZephpyr;
use Native\Electron\Traits\HasPreAndPostProcessing;
use Native\Electron\Traits\InstallsAppIcon;
use Native\Electron\Traits\LocatesPhpBinary;
use Native\Electron\Traits\PatchesPackagesJson;
use Native\Electron\Traits\PrunesVendorDirectory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Finder\Finder;
use ZipArchive;

use function Laravel\Prompts\intro;

#[AsCommand(
    name: 'native:bundle',
    description: 'Bundle your application for distribution.',
)]
class BundleCommand extends Command
{
    use CleansEnvFile;
    use CopiesToBuildDirectory;
    use HandlesZephpyr;
    use HasPreAndPostProcessing;
    use InstallsAppIcon;
    use LocatesPhpBinary;
    use PatchesPackagesJson;
    use PrunesVendorDirectory;

    protected $signature = 'native:bundle {--fetch} {--clear} {--without-cleanup}';

    private ?string $key;

    private string $zipPath;

    private string $zipName;

    public function handle(): int
    {
        // Remove the bundle
        if ($this->option('clear')) {
            if (file_exists(base_path('build/__nativephp_app_bundle'))) {
                unlink(base_path('build/__nativephp_app_bundle'));
            }

            $this->info('Bundle removed. Building in this state would be unsecure.');

            return static::SUCCESS;
        }

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

        $this->preProcess();

        $this->setAppNameAndVersion();
        intro('Copying App to build directory...');

        // We update composer.json later,
        $this->copyToBuildDirectory();

        $this->newLine();
        intro('Cleaning .env file...');
        $this->cleanEnvFile();

        $this->newLine();
        intro('Copying app icons...');
        $this->installIcon();

        $this->newLine();
        intro('Pruning vendor directory');
        $this->pruneVendorDirectory();

        $this->cleanEnvFile();

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
        $this->zipPath = $this->zipPath($this->zipName);

        // Create zip path
        if (! @mkdir(dirname($this->zipPath), recursive: true) && ! is_dir(dirname($this->zipPath))) {
            return false;
        }

        $zip = new ZipArchive;

        if ($zip->open($this->zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        $this->addFilesToZip($zip);

        $zip->close();

        return true;
    }

    private function checkComposerJson(): bool
    {
        $composerJson = json_decode(file_get_contents($this->buildPath('composer.json')), true);

        // // Fail if there is symlinked packages
        // foreach ($composerJson['repositories'] ?? [] as $repository) {
        //
        //     $symlinked = $repository['options']['symlink'] ?? true;
        //     if ($repository['type'] === 'path' && $symlinked) {
        //         $this->error('Symlinked packages are not supported. Please remove them from your composer.json.');
        //
        //         return false;
        //     }
        //     // Work with private packages but will not in the future
        //     // elseif ($repository['type'] === 'composer') {
        //     //     if (! $this->checkComposerPackageAuth($repository['url'])) {
        //     //         $this->error('Cannot authenticate with '.$repository['url'].'.');
        //     //         $this->error('Go to '.$this->baseUrl().' and add your composer package credentials.');
        //     //
        //     //         return false;
        //     //     }
        //     // }
        // }

        // Remove repositories with type path, we include symlinked packages
        if (! empty($composerJson['repositories'])) {

            $this->newLine();
            intro('Patching composer.json in development mode…');

            $filteredRepo = array_filter($composerJson['repositories'],
                fn ($repository) => $repository['type'] !== 'path');

            if (count($filteredRepo) !== count($composerJson['repositories'])) {
                $composerJson['repositories'] = $filteredRepo;
                file_put_contents($this->buildPath('composer.json'),
                    json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

                // Process::path($this->buildPath())
                //     ->run('composer install --no-dev', function (string $type, string $output) {
                //         echo $output;
                //     });
            }

        }

        return true;
    }

    // private function checkComposerPackageAuth(string $repositoryUrl): bool
    // {
    //     // Check if the user has authenticated the package on Zephpyr
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
        $this->newLine();
        intro('Creating zip archive…');

        $finder = (new Finder)->files()
            ->followLinks()
            // ->ignoreVCSIgnored(true) // TODO: Make our own list of ignored files
            ->in($this->buildPath())
            ->exclude([
                // We add those a few lines below and they are ignored by most .gitignore anyway
                'vendor',
                'node_modules',

                // Exclude the following directories
                'dist', // Compiled nativephp assets
                'build', // Compiled box assets
                'temp', // Temp files
                'tests', // Tests
                'auth.json', // Composer auth file
            ])
            ->exclude(config('nativephp.cleanup_exclude_files', []));

        $this->finderToZip($finder, $zip);

        // Why do I have to force this? please someone explain.
        if (file_exists($this->buildPath('public/build'))) {
            $this->finderToZip(
                (new Finder)->files()
                    ->followLinks()
                    ->in($this->buildPath('public/build')), $zip, 'public/build');
        }

        // Add .env file manually because Finder ignores VCS and dot files
        $zip->addFile($this->buildPath('.env'), '.env');

        // Add auth.json file to support private packages
        // WARNING: Only for testing purposes, don't uncomment this
        // $zip->addFile($this->buildPath('auth.json'), 'auth.json');

        // Custom binaries
        $binaryPath = Str::replaceStart($this->buildPath('vendor'), '', config('nativephp.binary_path'));

        // Add composer dependencies without unnecessary files
        $vendor = (new Finder)->files()
            ->exclude(array_filter([
                'nativephp/php-bin',
                'nativephp/electron/resources/js',
                '*/*/vendor', // Exclude sub-vendor directories
                $binaryPath,
            ]))
            ->in($this->buildPath('vendor'));

        $this->finderToZip($vendor, $zip, 'vendor');

        // Add javascript dependencies
        if (file_exists($this->buildPath('node_modules'))) {
            $nodeModules = (new Finder)->files()
                ->in($this->buildPath('node_modules'));

            $this->finderToZip($nodeModules, $zip, 'node_modules');
        }
    }

    private function finderToZip(Finder $finder, ZipArchive $zip, ?string $path = null): void
    {
        foreach ($finder as $file) {
            if ($file->getRealPath() === false) {
                continue;
            }

            $zipPath = str($path)->finish('/').$file->getRelativePathname();
            $zipPath = str_replace('\\', '/', $zipPath);

            $zip->addFile($file->getRealPath(), $zipPath);
        }
    }

    private function sendToZephpyr()
    {
        intro('Uploading zip to Zephpyr…');

        return Http::acceptJson()
            ->timeout(300) // 5 minutes
            ->withoutRedirecting() // Upload won't work if we follow redirects (it transform POST to GET)
            ->withToken(config('nativephp-internal.zephpyr.token'))
            ->attach('archive', fopen($this->zipPath, 'r'), $this->zipName)
            ->post($this->baseUrl().'api/v1/project/'.$this->key.'/build/');
    }

    private function fetchLatestBundle(): bool
    {
        intro('Fetching latest bundle…');

        $response = Http::acceptJson()
            ->withToken(config('nativephp-internal.zephpyr.token'))
            ->get($this->baseUrl().'api/v1/project/'.$this->key.'/build/download');

        if ($response->failed()) {

            if ($response->status() === 404) {
                $this->error('Project or bundle not found.');
            } elseif ($response->status() === 500) {
                $url = $response->json('url');

                if ($url) {
                    $this->error('Build failed. Inspect the build here: '.$url);
                } else {
                    $this->error('Build failed. Please try again later.');
                }
            } elseif ($response->status() === 503) {
                $retryAfter = intval($response->header('Retry-After'));
                $diff = now()->addSeconds($retryAfter);
                $diffMessage = $retryAfter <= 60 ? 'a minute' : $diff->diffForHumans(syntax: CarbonInterface::DIFF_ABSOLUTE);
                $this->warn('Bundle not ready. Please try again in '.$diffMessage.'.');
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
        $this->postProcess();

        if ($this->option('without-cleanup')) {
            return;
        }

        $previousBuilds = glob($this->zipPath().'/app_*.zip');
        $failedZips = glob($this->zipPath().'/app_*.part');

        $deleteFiles = array_merge($previousBuilds, $failedZips);

        if (empty($deleteFiles)) {
            return;
        }

        $this->line('Cleaning up…');

        foreach ($deleteFiles as $file) {
            @unlink($file);
        }
    }

    protected function buildPath(string $path = ''): string
    {
        return base_path('build/app/'.$path);
    }

    protected function zipPath(string $path = ''): string
    {
        return base_path('build/zip/'.$path);
    }

    protected function sourcePath(string $path = ''): string
    {
        return base_path($path);
    }
}
