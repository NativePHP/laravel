<?php

/**
 * This trait is responsible for copying over the app to the build directory.
 * It skips any ignored paths/globs during the copy step
 *
 * TODO: When more drivers/adapters are added, this should be relocated
 */

namespace Native\Electron\Traits;

use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

use function Laravel\Prompts\warning;

trait CopiesToBuildDirectory
{
    abstract protected function buildPath(string $path = ''): string;

    abstract protected function sourcePath(string $path = ''): string;

    public array $cleanupExcludeFiles = [
        // .git and dev directories
        '.git',
        'dist',
        'build',
        'temp',
        'docker',
        'packages',
        '**/.github',

        // Potentially containing sensitive info
        'auth.json', // Composer auth file
        'database/*.sqlite',
        'database/*.sqlite-shm',
        'database/*.sqlite-wal',

        'storage/framework/sessions/*',
        'storage/framework/testing/*',
        'storage/framework/cache/*',
        'storage/framework/views/*',
        'storage/logs/*',

        // Only needed for local testing
        'vendor/nativephp/electron/resources',
        'vendor/nativephp/electron/vendor',
        'vendor/nativephp/electron/bin',
        'vendor/nativephp/laravel/vendor',
        'vendor/nativephp/php-bin',

        // Also deleted in PrunesVendorDirectory after fresh composer install
        'vendor/bin',
    ];

    public function copyToBuildDirectory(): bool
    {
        $sourcePath = $this->sourcePath();
        $buildPath = $this->buildPath();
        $filesystem = new Filesystem;

        $patterns = array_merge(
            $this->cleanupExcludeFiles,
            config('nativephp.cleanup_exclude_files', []),
        );

        // Clean and create build directory
        $filesystem->remove($buildPath);
        $filesystem->mkdir($buildPath);

        // A filtered iterator that will exclude files matching our skip patterns
        $directory = new RecursiveDirectoryIterator(
            $sourcePath,
            RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::FOLLOW_SYMLINKS
        );

        $filter = new RecursiveCallbackFilterIterator($directory, function ($current) use ($patterns) {
            $relativePath = substr($current->getPathname(), strlen($this->sourcePath()) + 1);
            $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath); // Windows

            // Check each skip pattern against the current file/directory
            foreach ($patterns as $pattern) {

                // fnmatch supports glob patterns like "*.txt" or "cache/*"
                if (fnmatch($pattern, $relativePath)) {
                    return false;
                }
            }

            return true;
        });

        // Now we walk all directories & files and copy them over accordingly
        $iterator = new RecursiveIteratorIterator($filter, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item) {
            $target = $buildPath.DIRECTORY_SEPARATOR.substr($item->getPathname(), strlen($sourcePath) + 1);

            if ($item->isDir()) {
                if (! is_dir($target)) {
                    mkdir($target, 0755, true);
                }

                continue;
            }

            try {
                copy($item->getPathname(), $target);

                if (PHP_OS_FAMILY !== 'Windows') {
                    $perms = fileperms($item->getPathname());
                    if ($perms !== false) {
                        chmod($target, $perms);
                    }
                }
            } catch (Throwable $e) {
                warning('[WARNING] '.$e->getMessage().', file: '.$item->getPathname());
            }
        }

        $this->keepRequiredDirectories();

        return true;
    }

    private function keepRequiredDirectories()
    {
        // Electron build removes empty folders, so we have to create dummy files
        // dotfiles unfortunately don't work.
        $filesystem = new Filesystem;
        $buildPath = $this->buildPath();

        $filesystem->dumpFile("{$buildPath}/storage/framework/cache/_native.json", '{}');
        $filesystem->dumpFile("{$buildPath}/storage/framework/sessions/_native.json", '{}');
        $filesystem->dumpFile("{$buildPath}/storage/framework/testing/_native.json", '{}');
        $filesystem->dumpFile("{$buildPath}/storage/framework/views/_native.json", '{}');
        $filesystem->dumpFile("{$buildPath}/storage/app/public/_native.json", '{}');
        $filesystem->dumpFile("{$buildPath}/storage/logs/_native.json", '{}');
    }
}
