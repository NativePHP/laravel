<?php

namespace Native\Electron\Traits;

trait PhpBinaryTrait
{
	/**
	 * 
	 * @return string The path to the binary package directory
	 */
	protected function binaryPackageDirectory() : string
	{
		return 'vendor/nativephp/php-bin/';
	}

	/**
	 * Calculate the path to the PHP binary based on the OS
	 * @return string The path to the PHP binary (not including the filename)
	 */
	public function phpBinaryPath() : string
	{
		return $this->binaryPackageDirectory() . 'bin/' . (PHP_OS_FAMILY === 'Windows' ? 'win' : 'mac');
	}
}
