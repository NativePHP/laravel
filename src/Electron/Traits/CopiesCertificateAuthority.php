<?php

namespace Native\Electron\Traits;

use Composer\InstalledVersions;
use Native\Electron\ElectronServiceProvider;
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

            $vendorDirectory = realpath(InstalledVersions::getRootPackage()['install_path'].'/vendor');
            $phpBinaryDirectory = $vendorDirectory.'/nativephp/php-bin/';

            $certificateFileName = 'cacert.pem';
            $certFilePath = Path::join($phpBinaryDirectory, $certificateFileName);

            if (! file_exists($certFilePath)) {
                warning('CA Certificate not found at '.$certFilePath.'. Skipping copy.');

                return;
            }

            $copied = copy(
                $certFilePath,
                Path::join(ElectronServiceProvider::ELECTRON_PATH, 'resources', $certificateFileName)
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
