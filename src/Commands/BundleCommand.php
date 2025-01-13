<?php

namespace Native\Laravel\Commands;

use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Number;
use Native\Laravel\Commands\Traits\CleansEnvFile;
use Symfony\Component\Finder\Finder;
use ZipArchive;

class BundleCommand extends Command
{
    use CleansEnvFile;

    protected $signature = 'native:bundle {--fetch} {--without-cleanup}';

    protected $description = 'Bundle your application for distribution.';

    private ?string $key;

    private string $zipPath;

    private string $zipName;

    public function handle()
    {
        if (! $this->checkForZephpyrKey()) {
            return static::FAILURE;
        }

        if (! $this->checkForZephpyrToken()) {
            return static::FAILURE;
        }

        if (! $this->checkAuthenticated()) {
            $this->error('Invalid API token: check your ZEPHPYR_TOKEN on '.$this->baseUrl().'user/api-tokens');

            return static::FAILURE;
        }

        if ($this->option('fetch')) {
            if (! $this->fetchLatestBundle()) {
                $this->warn('Latest bundle not yet available. Try again soon.');

                return static::FAILURE;
            }

            $this->info('Latest bundle downloaded.');

            return static::SUCCESS;
        }

        // Package the app up into a zip
        if (! $this->zipApplication()) {
            $this->error("Failed to create zip archive at {$this->zipPath}.");

            return static::FAILURE;
        }

        // Send the zip file
        try {
            $result = $this->sendToZephpyr();
        } catch (ConnectionException $e) {
            // Timeout, etc.
            $this->error('Failed to send to Zephpyr: '.$e->getMessage());
            $this->cleanUp();

            return static::FAILURE;
        }

        if ($result->status() === 413) {
            $fileSize = Number::fileSize(filesize($this->zipPath));
            $this->error('The zip file is too large to upload to Zephpyr ('.$fileSize.'). Please contact support.');

            $this->cleanUp();

            return static::FAILURE;
        } elseif ($result->status() === 422) {
            $this->error('Zephpyr returned the following error:');
            $this->error(' → '.$result->json('message'));
            $this->cleanUp();

            return static::FAILURE;
        } elseif ($result->status() === 429) {
            $this->error('Zephpyr has a rate limit on builds per hour. Please try again in '.now()->addSeconds(intval($result->header('Retry-After')))->diffForHumans(syntax: CarbonInterface::DIFF_ABSOLUTE).'.');
            $this->cleanUp();

            return static::FAILURE;
        } elseif ($result->failed()) {
            $this->error("Failed to upload zip to Zephpyr. Error code: {$result->status()}");
            ray($result->body());
            $this->cleanUp();

            return static::FAILURE;
        }

        $this->info('Successfully uploaded to Zephpyr.');
        $this->line('Use native:bundle --fetch to retrieve the latest bundle.');

        $this->cleanUp();

        return static::SUCCESS;
    }

    protected function cleanUp(): void
    {
        if ($this->option('without-cleanup')) {
            return;
        }

        $this->line('Cleaning up…');

        $previousBuilds = glob(base_path('temp/app_*.zip'));
        $failedZips = glob(base_path('temp/app_*.part'));

        $deleteFiles = array_merge($previousBuilds, $failedZips);
        foreach ($deleteFiles as $file) {
            @unlink($file);
        }
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

    private function addFilesToZip(ZipArchive $zip): void
    {
        // TODO: Check the composer.json to make sure there are no symlinked
        // or private packages as these will be a pain later

        // TODO: Fail if there is symlinked packages
        // TODO: For private packages: make an endpoint to check if user gave us their credentials

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
                'tests', // Tests
                ...config('nativephp.cleanup_exclude_files', []), // User defined
            ]);

        $this->finderToZip($app, $zip);

        $vendor = (new Finder)->files()
            // ->followLinks()
            ->exclude(array_filter([
                'nativephp/php-bin',
                'nativephp/electron/resources/js',
                'nativephp/*/vendor',
                config('nativephp.binary_path'), // User defined binary paths
            ]))
            ->in(base_path('vendor'));

        $this->finderToZip($vendor, $zip, 'vendor');

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

    private function baseUrl(): string
    {
        return str(config('nativephp-internal.zephpyr.host'))->finish('/');
    }

    private function sendToZephpyr()
    {
        $this->line('Uploading zip to Zephpyr…');

        return Http::acceptJson()
            ->timeout(300) // 5 minutes
            ->withoutRedirecting() // Upload won't work if we follow the redirect
            ->withToken(config('nativephp-internal.zephpyr.token'))
            ->attach('archive', fopen($this->zipPath, 'r'), $this->zipName)
            ->post($this->baseUrl().'api/v1/project/'.$this->key.'/build/');
    }

    private function checkAuthenticated()
    {
        $this->line('Checking authentication…');

        return Http::acceptJson()
            ->withToken(config('nativephp-internal.zephpyr.token'))
            ->get($this->baseUrl().'api/v1/user')->successful();
    }

    private function fetchLatestBundle(): bool
    {
        $response = Http::acceptJson()
            ->withToken(config('nativephp-internal.zephpyr.token'))
            ->get($this->baseUrl().'api/v1/project/'.$this->key.'/build/download');

        if ($response->failed()) {
            return false;
        }

        file_put_contents(base_path('build/__nativephp_app_bundle'), $response->body());

        return true;
    }

    private function checkForZephpyrKey()
    {
        $this->key = config('nativephp-internal.zephpyr.key');

        if (! $this->key) {
            $this->line('');
            $this->warn('No ZEPHPYR_KEY found. Cannot bundle!');
            $this->line('');
            $this->line('Add this app\'s ZEPHPYR_KEY to its .env file:');
            $this->line(base_path('.env'));
            $this->line('');
            $this->info('Not set up with Zephpyr yet? Secure your NativePHP app builds and more!');
            $this->info('Check out '.$this->baseUrl().'');
            $this->line('');

            return false;
        }

        return true;
    }

    private function checkForZephpyrToken()
    {
        if (! config('nativephp-internal.zephpyr.token')) {
            $this->line('');
            $this->warn('No ZEPHPYR_TOKEN found. Cannot bundle!');
            $this->line('');
            $this->line('Add your api ZEPHPYR_TOKEN to its .env file:');
            $this->line(base_path('.env'));
            $this->line('');
            $this->info('Not set up with Zephpyr yet? Secure your NativePHP app builds and more!');
            $this->info('Check out '.$this->baseUrl().'');
            $this->line('');

            return false;
        }

        return true;
    }
}
