<?php

namespace Native\Electron\Concerns;

trait LocatesPhpBinary
{
    /**
     * @return string The path to the binary package directory
     */
    protected function binaryPackageDirectory(): string
    {
        return env('NATIVEPHP_PHP_BINARY_PATH', 'vendor/nativephp/php-bin/');
    }

    /**
     * Returns the path to the PHP binary.
     *
     * @return string The path to the PHP binary (not including the filename)
     */
    public function phpBinaryPath(): string
    {
        return $this->binaryPackageDirectory().'bin/';
    }
}
