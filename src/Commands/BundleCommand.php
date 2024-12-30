<?php

namespace Native\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Native\Electron\Traits\CleansEnvFile;
use Symfony\Component\Finder\Finder;
use ZipArchive;

class BundleCommand extends Command
{
    use CleansEnvFile;

    protected $signature = 'native:bundle {--fetch}';

    protected $description = 'Bundle your application for distribution.';

    private ?string $key;

    private string $zipPath;

    private string $zipName;

    public function handle()
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
            $this->info('Check out https://zephpyr.com');
            $this->line('');

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
        dd($result = $this->sendToZephpyr());

        if ($result->failed()) {
            $this->error("Failed to upload zip [{$this->zipPath}] to Zephpyr.");

            return static::FAILURE;
        }

        @unlink($this->zipPath);

        $this->info('Successfully uploaded to Zephpyr.');
        $this->line('Use native:bundle --fetch to retrieve the latest bundle.');

        return static::SUCCESS;
    }

    private function zipApplication(): bool
    {
        $this->zipName = 'app_'.str()->random(8).'.zip';
        $this->zipPath = storage_path($this->zipName);

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
        // TODO: Check the composer.json to make sure there are no symlinked or private packages as these will be a
        // pain later

        $app = (new Finder)->files()
            ->followLinks()
            ->ignoreVCSIgnored(true)
            ->in(base_path())
            ->exclude([
                'vendor',
                'dist',
                'build',
                'tests',
                ...config('nativephp.cleanup_exclude_files', []),
            ]);

        $this->finderToZip($app, $zip);

        $vendor = (new Finder)->files()
            ->exclude([
                'nativephp/php-bin',
                'nativephp/electron/resources/js',
                'nativephp/*/vendor',
            ])
            ->in(base_path('vendor'));

        $this->finderToZip($vendor, $zip, 'vendor');

        $nodeModules = (new Finder)->files()
            ->in(base_path('node_modules'));

        $this->finderToZip($nodeModules, $zip, 'node_modules');
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
        return Http::withToken(config('nativephp-internal.zephpyr.token'))
            ->attach('archive', fopen($this->zipPath, 'r'), $this->zipName)
            ->post(str(config('nativephp-internal.zephpyr.host'))->finish('/').'api/build/'.$this->key);
    }

    private function fetchLatestBundle(): bool
    {
        $response = Http::withToken(config('nativephp-internal.zephpyr.token'))
            ->get(str(config('nativephp-internal.zephpyr.host'))->finish('/').'api/download/'.$this->key);

        if ($response->failed()) {
            return false;
        }

        file_put_contents(base_path('build/__nativephp_app_bundle'), $response->body());

        return true;
    }
}
