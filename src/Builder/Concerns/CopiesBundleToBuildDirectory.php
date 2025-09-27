<?php

namespace Native\Desktop\Builder\Concerns;

use Symfony\Component\Filesystem\Filesystem;

use function Laravel\Prompts\warning;

trait CopiesBundleToBuildDirectory
{
    use CopiesToBuildDirectory;

    protected static string $bundlePath = 'build/__nativephp_app_bundle';

    public function hasBundled(): bool
    {
        return (new Filesystem)->exists($this->sourcePath(self::$bundlePath));
    }

    public function copyBundleToBuildDirectory(): bool
    {
        $filesystem = new Filesystem;

        echo 'Copying secure app bundle to build directory...'.PHP_EOL;
        echo 'From: '.realpath(dirname($this->sourcePath(self::$bundlePath))).PHP_EOL;
        echo 'To: '.realpath(dirname($this->buildPath('app/'.self::$bundlePath))).PHP_EOL;

        // Clean and create build directory
        $filesystem->remove($this->buildPath('app'));
        $filesystem->mkdir($this->buildPath('app'));

        $filesToCopy = [
            self::$bundlePath,
            // '.env',
        ];
        foreach ($filesToCopy as $file) {
            $filesystem->copy($this->sourcePath($file), $this->buildPath('app/'.$file), true);
        }
        $this->keepRequiredDirectories();

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
