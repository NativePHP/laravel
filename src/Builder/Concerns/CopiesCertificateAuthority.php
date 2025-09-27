<?php

namespace Native\Desktop\Builder\Concerns;

use Native\Desktop\Drivers\Electron\ElectronServiceProvider;
use Native\Desktop\Support\Composer;

use function Laravel\Prompts\error;
use function Laravel\Prompts\warning;

trait CopiesCertificateAuthority
{
    abstract public function buildPath(string $path = ''): string;

    public function copyCertificateAuthority(): void
    {
        try {
            $srcPath = Composer::phpPackagePath('cacert.pem');
            $destPath = ElectronServiceProvider::buildPath();

            if (! file_exists($srcPath)) {
                warning('CA Certificate not found at '.$srcPath.'. Skipping copy.');

                return;
            }

            $copied = copy(
                $srcPath,
                "{$destPath}/cacert.pem"
            );

            if (! $copied) {
                // It returned false, but doesn't give a reason why.
                throw new \Exception('copy() failed for an unknown reason.');
            }
        } catch (\Throwable $e) {
            error('Failed to copy CA Certificate: '.$e->getMessage());
        }
    }
}
