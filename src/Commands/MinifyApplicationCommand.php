<?php

namespace Native\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Native\Laravel\Compactor\Php;
use Symfony\Component\Finder\Finder;

class MinifyApplicationCommand extends Command
{
    protected $signature = 'native:minify {app}';

    public function handle()
    {
        $appPath = realpath($this->argument('app'));

        if (! is_dir($appPath)) {
            $this->error('The app path is not a directory');

            return;
        }

        $this->info('Minifying application…');

        $this->cleanUpEnvFile($appPath);
        $this->removeIgnoredFilesAndFolders($appPath);

        $compactor = new Php;

        $phpFiles = Finder::create()
            ->files()
            ->name('*.php')
            ->in($appPath);

        foreach ($phpFiles as $phpFile) {
            $minifiedContent = $compactor->compact($phpFile->getRealPath(), $phpFile->getContents());
            file_put_contents($phpFile->getRealPath(), $minifiedContent);
        }
    }

    protected function cleanUpEnvFile(string $appPath): void
    {
        $envFile = $appPath.'/.env';

        if (! file_exists($envFile)) {
            return;
        }

        $this->info('Cleaning up .env file…');

        $cleanUpKeys = config('nativephp.cleanup_env_keys', []);

        $envContent = file_get_contents($envFile);
        $envValues = collect(explode("\n", $envContent))
            ->filter(function (string $line) use ($cleanUpKeys) {
                $key = Str::before($line, '=');

                return ! Str::is($cleanUpKeys, $key);
            })
            ->join("\n");

        file_put_contents($envFile, $envValues);
    }

    protected function removeIgnoredFilesAndFolders(string $appPath): void
    {
        $this->info('Cleaning up ignored files and folders…');

        $itemsToRemove = config('nativephp.cleanup_exclude_files', []);

        foreach ($itemsToRemove as $item) {
            $fullPath = $appPath.'/'.$item;

            if (file_exists($fullPath)) {
                if (is_dir($fullPath)) {
                    $this->deleteDirectoryRecursive($fullPath);
                } else {
                    array_map('unlink', glob($fullPath));
                }
            } else {
                foreach (glob($item) as $pathFound) {
                    unlink($pathFound);
                }
            }
        }
    }

    private function deleteDirectoryRecursive(string $directory): bool
    {
        if (! file_exists($directory)) {
            return true;
        }

        if (! is_dir($directory)) {
            return unlink($directory);
        }

        foreach (scandir($directory) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (! $this->deleteDirectoryRecursive($directory.'/'.$item)) {
                return false;
            }
        }

        return rmdir($directory);
    }
}
