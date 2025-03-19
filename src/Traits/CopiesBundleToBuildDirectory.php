<?php

namespace Native\Electron\Traits;

use Symfony\Component\Filesystem\Filesystem;

use function Laravel\Prompts\warning;

trait CopiesBundleToBuildDirectory
{
    use CopiesToBuildDirectory;

    protected static string $bundlePath = 'build/__nativephp_app_bundle';

    protected function hasBundled(): bool
    {
        return (new Filesystem)->exists($this->sourcePath(self::$bundlePath));
    }

    public function copyBundleToBuildDirectory(): bool
    {
        $filesystem = new Filesystem;

        $this->line('Copying secure app bundle to build directory...');
        $this->line('From: '.realpath(dirname($this->sourcePath(self::$bundlePath))));
        $this->line('To: '.realpath(dirname($this->buildPath(self::$bundlePath))));

        // Clean and create build directory
        $filesystem->remove($this->buildPath());
        $filesystem->mkdir($this->buildPath());

        $filesToCopy = [
            self::$bundlePath,
            // '.env',
        ];
        foreach ($filesToCopy as $file) {
            $filesystem->copy($this->sourcePath($file), $this->buildPath($file), true);
        }
        // $this->keepRequiredDirectories();

        return true;
    }

    public function warnUnsecureBuild(): void
    {
        warning('===================================================================');
        warning('                    * * * INSECURE BUILD * * *');
        warning('===================================================================');
        warning('Secure app bundle not found! Building with exposed source files.');
        warning('See https://nativephp.com/docs/publishing/building#security');
        warning('===================================================================');
    }
}
