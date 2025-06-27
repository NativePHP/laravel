<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Native\Electron\Facades\Updater;
use Native\Electron\Traits\CleansEnvFile;
use Native\Electron\Traits\CopiesBundleToBuildDirectory;
use Native\Electron\Traits\CopiesCertificateAuthority;
use Native\Electron\Traits\HasPreAndPostProcessing;
use Native\Electron\Traits\InstallsAppIcon;
use Native\Electron\Traits\LocatesPhpBinary;
use Native\Electron\Traits\OsAndArch;
use Native\Electron\Traits\PatchesPackagesJson;
use Native\Electron\Traits\PrunesVendorDirectory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\Process as SymfonyProcess;

use function Laravel\Prompts\intro;

#[AsCommand(
    name: 'native:build',
    description: 'Build the NativePHP application for the specified operating system and architecture.',
)]
class BuildCommand extends Command
{
    use CleansEnvFile;
    use CopiesBundleToBuildDirectory;
    use CopiesCertificateAuthority;
    use HasPreAndPostProcessing;
    use InstallsAppIcon;
    use LocatesPhpBinary;
    use OsAndArch;
    use PatchesPackagesJson;
    use PrunesVendorDirectory;

    protected $signature = 'native:build
        {os? : The operating system to build for (all, linux, mac, win)}
        {arch? : The Processor Architecture to build for (x64, x86, arm64)}
        {--publish : to publish the app}';

    protected array $availableOs = ['win', 'linux', 'mac', 'all'];

    private string $buildCommand;

    private string $buildOS;

    protected function buildPath(string $path = ''): string
    {
        return __DIR__.'/../../resources/js/resources/app/'.$path;
    }

    protected function sourcePath(string $path = ''): string
    {
        return base_path($path);
    }

    public function handle(): void
    {
        $this->buildOS = $this->selectOs($this->argument('os'));

        $this->buildCommand = 'build';
        if ($this->buildOS != 'all') {
            $arch = $this->selectArchitectureForOs($this->buildOS, $this->argument('arch'));

            $this->buildOS .= $arch != 'all' ? "-{$arch}" : '';
        }

        if ($this->option('publish')) {
            $this->buildCommand = 'publish';
        }

        if ($this->hasBundled()) {
            $this->buildBundle();
        } else {
            $this->warnUnsecureBuild();
            $this->buildUnsecure();
        }
    }

    private function buildBundle(): void
    {
        $this->setAppNameAndVersion();

        $this->updateElectronDependencies();

        $this->newLine();
        intro('Copying Bundle to build directory...');
        $this->copyBundleToBuildDirectory();
        $this->keepRequiredDirectories();

        $this->newLine();
        $this->copyCertificateAuthorityCertificate();

        $this->newLine();
        intro('Copying app icons...');
        $this->installIcon();

        $this->buildOrPublish();
    }

    private function buildUnsecure(): void
    {
        $this->preProcess();

        $this->setAppNameAndVersion();

        $this->updateElectronDependencies();

        $this->newLine();
        intro('Copying App to build directory...');
        $this->copyToBuildDirectory();

        $this->newLine();
        $this->copyCertificateAuthorityCertificate();

        $this->newLine();
        intro('Cleaning .env file...');
        $this->cleanEnvFile();

        $this->newLine();
        intro('Copying app icons...');
        $this->installIcon();

        $this->newLine();
        intro('Pruning vendor directory');
        $this->pruneVendorDirectory();

        $this->buildOrPublish();

        $this->postProcess();
    }

    protected function getEnvironmentVariables(): array
    {
        return array_merge(
            [
                'APP_PATH' => $this->sourcePath(),
                'APP_URL' => config('app.url'),
                'NATIVEPHP_BUILDING' => true,
                'NATIVEPHP_PHP_BINARY_VERSION' => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION,
                'NATIVEPHP_PHP_BINARY_PATH' => $this->sourcePath($this->phpBinaryPath()),
                'NATIVEPHP_APP_NAME' => config('app.name'),
                'NATIVEPHP_APP_ID' => config('nativephp.app_id'),
                'NATIVEPHP_APP_VERSION' => config('nativephp.version'),
                'NATIVEPHP_APP_COPYRIGHT' => config('nativephp.copyright'),
                'NATIVEPHP_APP_FILENAME' => Str::slug(config('app.name')),
                'NATIVEPHP_APP_AUTHOR' => config('nativephp.author'),
                'NATIVEPHP_UPDATER_CONFIG' => json_encode(Updater::builderOptions()),
                'NATIVEPHP_DEEPLINK_SCHEME' => config('nativephp.deeplink_scheme'),
                // Notarization
                'NATIVEPHP_APPLE_ID' => config('nativephp-internal.notarization.apple_id'),
                'NATIVEPHP_APPLE_ID_PASS' => config('nativephp-internal.notarization.apple_id_pass'),
                'NATIVEPHP_APPLE_TEAM_ID' => config('nativephp-internal.notarization.apple_team_id'),
            ],
            Updater::environmentVariables(),
        );
    }

    private function updateElectronDependencies(): void
    {
        $this->newLine();
        intro('Updating Electron dependencies...');
        Process::path(__DIR__.'/../../resources/js/')
            ->env($this->getEnvironmentVariables())
            ->forever()
            ->run('npm ci', function (string $type, string $output) {
                echo $output;
            });
    }

    private function buildOrPublish(): void
    {
        $this->newLine();
        intro((($this->buildCommand == 'publish') ? 'Publishing' : 'Building')." for {$this->buildOS}");
        Process::path(__DIR__.'/../../resources/js/')
            ->env($this->getEnvironmentVariables())
            ->forever()
            ->tty(SymfonyProcess::isTtySupported() && ! $this->option('no-interaction'))
            ->run("npm run {$this->buildCommand}:{$this->buildOS}", function (string $type, string $output) {
                echo $output;
            });
    }
}
