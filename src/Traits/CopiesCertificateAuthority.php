<?php

namespace Native\Electron\Traits;

use Symfony\Component\Filesystem\Path;

use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\warning;

trait CopiesCertificateAuthority
{
    protected function copyCertificateAuthorityCertificate(): void
    {
        try {
            intro('Copying latest CA Certificate...');

            $phpBinaryDirectory = base_path('vendor/nativephp/php-bin/');

            // Check if the class this trait is used in also uses LocatesPhpBinary
            /* @phpstan-ignore-next-line */
            if (method_exists($this, 'phpBinaryPath')) {
                // Get binary directory but up one level
                $phpBinaryDirectory = dirname(base_path($this->phpBinaryPath()));
            }

            $certificateFileName = 'cacert.pem';
            $certFilePath = Path::join($phpBinaryDirectory, $certificateFileName);

            if (! file_exists($certFilePath)) {
                warning('CA Certificate not found at '.$certFilePath.'. Skipping copy.');

                return;
            }

            $copied = copy(
                $certFilePath,
                Path::join(base_path('vendor/nativephp/electron/resources/js/resources'), $certificateFileName)
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
