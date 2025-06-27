<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use Native\Electron\Traits\CopiesCertificateAuthority;
use Native\Electron\Traits\Developer;
use Native\Electron\Traits\Installer;
use Native\Electron\Traits\InstallsAppIcon;
use Native\Electron\Traits\PatchesPackagesJson;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;

#[AsCommand(
    name: 'native:serve',
    description: 'Start the NativePHP development server with the Electron app',
)]
class DevelopCommand extends Command
{
    use CopiesCertificateAuthority;
    use Developer;
    use Installer;
    use InstallsAppIcon;
    use PatchesPackagesJson;

    protected $signature = 'native:serve {--no-queue} {--D|no-dependencies} {--installer=npm}';

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

        $this->copyCertificateAuthorityCertificate();

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
        $pList = file_get_contents(__DIR__.'/../../resources/js/node_modules/electron/dist/Electron.app/Contents/Info.plist');

        // Change the CFBundleName to the correct app name
        $pattern = '/(<key>CFBundleName<\/key>\s+<string>)(.*?)(<\/string>)/m';
        $pList = preg_replace($pattern, '$1'.config('app.name').'$3', $pList);

        $pattern = '/(<key>CFBundleDisplayName<\/key>\s+<string>)(.*?)(<\/string>)/m';
        $pList = preg_replace($pattern, '$1'.config('app.name').'$3', $pList);

        file_put_contents(__DIR__.'/../../resources/js/node_modules/electron/dist/Electron.app/Contents/Info.plist', $pList);
    }
}
