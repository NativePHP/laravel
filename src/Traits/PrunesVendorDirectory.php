<?php

/**
 * TODO: When more drivers/adapters are added, this should be relocated
 */

namespace Native\Electron\Traits;

use Illuminate\Support\Facades\Process;
use Symfony\Component\Filesystem\Filesystem;

trait PrunesVendorDirectory
{
    abstract protected function buildPath(string $path = ''): string;

    protected function pruneVendorDirectory()
    {
        Process::path($this->buildPath())
            ->timeout(300)
            ->run('composer install --no-dev', function (string $type, string $output) {
                echo $output;
            });

        $filesystem = new Filesystem;
        $filesystem->remove([
            $this->buildPath('/vendor/bin'),
            $this->buildPath('/vendor/nativephp/php-bin'),
        ]);

        // Remove custom php binary package directory
        $binaryPackageDirectory = $this->binaryPackageDirectory();
        if (! empty($binaryPackageDirectory) && $filesystem->exists($this->buildPath($binaryPackageDirectory))) {
            $filesystem->remove($this->buildPath($binaryPackageDirectory));
        }
    }
}
