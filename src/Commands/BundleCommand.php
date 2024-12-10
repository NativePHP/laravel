<?php

namespace Native\Laravel\Commands;

use Illuminate\Console\Command;
use Native\Electron\Traits\CleansEnvFile;
use Native\Laravel\NativeServiceProvider;
use Symfony\Component\Finder\Finder;
use ZipArchive;

class BundleCommand extends Command
{
    use CleansEnvFile;

    protected $name = 'native:bundle';

    protected $description = 'Bundle your application for distribution.';

    private ?string $key;
    private string $zipPath;
    private string $zipName;

    public function handle()
    {
        $this->key = config('nativephp-internal.zephpyr.key');

        if (!$this->key) {
            $this->line('');
            $this->warn('No ZEPHPYR_SECRET found. Cannot bundle!');
            $this->line('');
            $this->line('Add this app\'s ZEPHPYR_SECRET to its .env file:');
            $this->line(base_path('.env'));
            $this->line('');
            $this->info('Not set up with Zephpyr yet? Secure your NativePHP app builds and more!');
            $this->info('Check out https://zephpyr.com');
            $this->line('');

            return static::FAILURE;
        }

        // Package the app up into a zip
        if (! $this->zipApplication()) {
            $this->error("Failed to create zip archive at {$this->zipPath}.");
            return static::FAILURE;
        }

        // Send the zip file
        if (! $this->sendToZephpyr()) {
            $this->error("Failed to upload zip [{$this->zipPath}] to Zephpyr.");
            return static::FAILURE;
        }

        return static::SUCCESS;
    }

    private function zipApplication(): bool
    {
        $this->zipName = 'app_' . str()->random(8) . '.zip';
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
        $app = (new Finder())->files()
            ->followLinks()
            ->ignoreVCSIgnored(true)
            ->in(base_path())
            ->exclude([
                'tests',
                ...config('nativephp.cleanup_exclude_files', []),
            ]);

        $this->finderToZip($app, $zip);

        $vendor = (new Finder())->files()
            ->exclude([
                'vendor/nativephp/php-bin',
            ])
            ->in(base_path('vendor'));

        $this->finderToZip($vendor, $zip);

        $nodeModules = (new Finder())->files()
            ->in(base_path('node_modules'));

        $this->finderToZip($nodeModules, $zip);

        $env = (new Finder())->files()
            ->ignoreDotFiles(false)
            ->name('.env')
            ->in(base_path());

        $this->finderToZip($env, $zip);
    }

    private function finderToZip(Finder $finder, ZipArchive $zip): void
    {
        foreach ($finder as $file) {
            dump([$file->getRealPath(), $file->getRelativePath()]);
            $zip->addFile($file->getRealPath(), $file->getRelativePathname());
        }
    }

    private function sendToZephpyr(): bool
    {
        return false;
        $response = Http::attach('archive', fopen($this->zipPath, 'r'), $this->zipName)
            ->post(config('nativephp-internal.zephpyr.host'), [
                'key' => $this->key,
            ]);

        return $response->successful();
    }
}
