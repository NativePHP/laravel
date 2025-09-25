<?php

namespace Native\Desktop\Builder\Traits;

use Composer\InstalledVersions;
use Symfony\Component\Filesystem\Path;

use function Laravel\Prompts\error;
use function Laravel\Prompts\warning;

trait CopiesCertificateAuthority
{
    abstract public function buildPath(string $path = ''): string;

    public function copyCertificateAuthority(string $path): void
    {
        try {
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
                "{$path}/{$certificateFileName}"
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
