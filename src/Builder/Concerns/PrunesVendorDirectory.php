<?php

namespace Native\Desktop\Builder\Concerns;

use Illuminate\Support\Facades\Process;
use Symfony\Component\Filesystem\Filesystem;

trait PrunesVendorDirectory
{
    abstract public function buildPath(string $path = ''): string;

    public function pruneVendorDirectory()
    {
        Process::path($this->buildPath())
            ->timeout(300)
            ->run('composer install --no-dev', function (string $type, string $output) {
                echo $output;
            });

        $filesystem = new Filesystem;
        $filesystem->remove([
            $this->buildPath('app/vendor/bin'),
            $this->buildPath('app/vendor/nativephp/php-bin'),
        ]);

        // Remove custom php binary package directory
        $binaryPackageDirectory = $this->binaryPackageDirectory();
        if (! empty($binaryPackageDirectory) && $filesystem->exists($this->buildPath($binaryPackageDirectory))) {
            $filesystem->remove($this->buildPath('app', $binaryPackageDirectory));
        }
    }
}
