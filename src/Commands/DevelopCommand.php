<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
use Native\Electron\Traits\Developer;
use Native\Electron\Traits\Installer;

class DevelopCommand extends Command
{
    use Installer, Developer;

    protected $signature = 'native:serve {--no-queue} {--D|no-dependencies} {--installer=npm}';

    public function handle()
    {
        intro('Starting NativePHP dev server…');

        note('Fetching latest dependencies…');

        if (! $this->option('no-dependencies')) {
            $this->installNPMDependencies(
                force: ! $this->option('no-dependencies'),
                installer: $this->option('installer'
                )
            );
        }

        note('Starting NativePHP app');

        if (PHP_OS_FAMILY === 'Darwin') {
            $this->patchPlist();
        }

        $this->runDeveloper(installer: $this->option('installer'), skip_queue: $this->option('no-queue'));
    }

    /**
     * Patch Electron's Info.plist to show the correct app name
     * during development.
     *
     * @return void
     */
    protected function patchPlist()
    {
        $pList = file_get_contents(__DIR__.'/../../resources/js/node_modules/electron/dist/Electron.app/Contents/Info.plist');

        // Change the CFBundleName to the correct app name
        $pattern = '/(<key>CFBundleName<\/key>\s+<string>)(.*?)(<\/string>)/m';
        $pList = preg_replace($pattern, '$1'.config('app.name').'$3', $pList);

        $pattern = '/(<key>CFBundleDisplayName<\/key>\s+<string>)(.*?)(<\/string>)/m';
        $pList = preg_replace($pattern, '$1'.config('app.name').'$3', $pList);

        file_put_contents(__DIR__.'/../../resources/js/node_modules/electron/dist/Electron.app/Contents/Info.plist', $pList);
    }
}
