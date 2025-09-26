<?php

namespace Native\Desktop\Builder\Concerns;

use Native\Desktop\Support\Composer;

use function Laravel\Prompts\error;
use function Laravel\Prompts\warning;

trait CopiesCertificateAuthority
{
    abstract public function buildPath(string $path = ''): string;

    public function copyCertificateAuthority(string $path): void
    {
        try {
            $certFilePath = Composer::phpPackagePath('cacert.pem');

            if (! file_exists($certFilePath)) {
                warning('CA Certificate not found at '.$certFilePath.'. Skipping copy.');

                return;
            }

            $copied = copy(
                $certFilePath,
                "{$path}/cacert.pem"
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
