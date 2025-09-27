<?php

namespace Native\Desktop\Drivers\Electron\Traits;

use Illuminate\Support\Facades\File;
use Native\Desktop\Support\Composer;
use Symfony\Component\Filesystem\Filesystem;

trait CreatesElectronProject
{
    public function createElectronProject($installPath)
    {
        $sourcePath = Composer::desktopPackagePath('resources/electron');

        File::ensureDirectoryExists($installPath, 0755, true);

        (new Filesystem)->mirror(
            $sourcePath,
            $installPath,
            options: [
                'override' => true,
                'delete' => true,
            ]
        );
    }
}
