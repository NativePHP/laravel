<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Filesystem\Filesystem;

use function Laravel\Prompts\intro;

class ResetCommand extends Command
{
    protected $signature = 'native:reset';

    protected $description = 'Clear all build and dist files';

    public function handle(): int
    {
        intro('Clearing build and dist directories...');

        // Removing and recreating the native serve resource path
        $nativeServeResourcePath = realpath(__DIR__.'/../../resources/js/resources/app/');
        $this->line('Clearing: '.$nativeServeResourcePath);

        $filesystem = new Filesystem;
        $filesystem->remove($nativeServeResourcePath);
        $filesystem->mkdir($nativeServeResourcePath);

        // Removing the bundling directories
        $bundlingPath = base_path('build/');
        $this->line('Clearing: '.$bundlingPath);

        if ($filesystem->exists($bundlingPath)) {
            $filesystem->remove($bundlingPath);
        }

        // Removing the built path
        $builtPath = base_path('dist/');
        $this->line('Clearing: '.$builtPath);

        if ($filesystem->exists($builtPath)) {
            $filesystem->remove($builtPath);
        }

        return 0;
    }
}
