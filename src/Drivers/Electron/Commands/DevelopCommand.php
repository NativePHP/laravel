<?php

namespace Native\Desktop\Drivers\Electron\Commands;

use Illuminate\Console\Command;
use Native\Desktop\Builder\Builder;
use Native\Desktop\Drivers\Electron\ElectronServiceProvider;
use Native\Desktop\Drivers\Electron\Traits\Developer;
use Native\Desktop\Drivers\Electron\Traits\Installer;
use Native\Desktop\Drivers\Electron\Traits\InstallsAppIcon;
use Native\Desktop\Drivers\Electron\Traits\PatchesPackagesJson;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;

#[AsCommand(
    name: 'native:serve',
    description: 'Start the NativePHP development server with the Electron app',
)]
class DevelopCommand extends Command
{
    use Developer;
    use Installer;
    use InstallsAppIcon;
    use PatchesPackagesJson;

    protected $signature = 'native:serve {--no-queue} {--D|no-dependencies} {--installer=npm}';

    public function __construct(
        protected Builder $builder
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        intro('Starting NativePHP dev server…');

        note('Fetching latest dependencies…');

        if (! $this->option('no-dependencies')) {
            $this->installNPMDependencies(
                force: true,
                installer: $this->option('installer'),
                withoutInteraction: $this->option('no-interaction')
            );
        }

        note('Starting NativePHP app');

        if (PHP_OS_FAMILY === 'Darwin') {
            $this->patchPlist();
        }

        $this->setAppNameAndVersion(developmentMode: true);

        $this->installIcon();

        $this->builder->copyCertificateAuthority(path: ElectronServiceProvider::ELECTRON_PATH.'/resources');

        $this->runDeveloper(
            installer: $this->option('installer'),
            skip_queue: $this->option('no-queue'),
            withoutInteraction: $this->option('no-interaction')
        );
    }

    /**
     * Patch Electron's Info.plist to show the correct app name
     * during development.
     */
    protected function patchPlist(): void
    {
        $pList = file_get_contents(ElectronServiceProvider::ELECTRON_PATH.'/node_modules/electron/dist/Electron.app/Contents/Info.plist');

        // Change the CFBundleName to the correct app name
        $pattern = '/(<key>CFBundleName<\/key>\s+<string>)(.*?)(<\/string>)/m';
        $pList = preg_replace($pattern, '$1'.config('app.name').'$3', $pList);

        $pattern = '/(<key>CFBundleDisplayName<\/key>\s+<string>)(.*?)(<\/string>)/m';
        $pList = preg_replace($pattern, '$1'.config('app.name').'$3', $pList);

        file_put_contents(ElectronServiceProvider::ELECTRON_PATH.'/node_modules/electron/dist/Electron.app/Contents/Info.plist', $pList);
    }
}
