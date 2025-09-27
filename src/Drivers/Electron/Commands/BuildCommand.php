<?php

namespace Native\Desktop\Drivers\Electron\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Native\Desktop\Builder\Builder;
use Native\Desktop\Drivers\Electron\ElectronServiceProvider;
use Native\Desktop\Drivers\Electron\Facades\Updater;
use Native\Desktop\Drivers\Electron\Traits\InstallsAppIcon;
use Native\Desktop\Drivers\Electron\Traits\OsAndArch;
use Native\Desktop\Drivers\Electron\Traits\PatchesPackagesJson;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\Process as SymfonyProcess;

use function Laravel\Prompts\intro;

#[AsCommand(
    name: 'native:build',
    description: 'Build the NativePHP application for the specified operating system and architecture.',
)]
class BuildCommand extends Command
{
    use InstallsAppIcon;
    use OsAndArch;
    use PatchesPackagesJson;

    protected $signature = 'native:build
        {os? : The operating system to build for (all, linux, mac, win)}
        {arch? : The Processor Architecture to build for (x64, x86, arm64)}
        {--publish : to publish the app}';

    protected array $availableOs = ['win', 'linux', 'mac', 'all'];

    private string $buildCommand;

    private string $buildOS;

    public function __construct(
        protected Builder $builder
    ) {
        parent::__construct();
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

        if ($this->builder->hasBundled()) {
            $this->buildBundle();
        } else {
            $this->builder->warnUnsecureBuild();
            $this->buildUnsecure();
        }
    }

    private function buildBundle(): void
    {
        $this->builder->preProcess();

        $this->setAppNameAndVersion();

        $this->updateElectronDependencies();

        $this->newLine();
        intro('Copying Bundle to build directory...');
        $this->builder->copyBundleToBuildDirectory();

        $this->newLine();
        intro('Copying latest CA Certificate...');
        $this->builder->copyCertificateAuthority();

        $this->newLine();
        intro('Copying app icons...');
        $this->installIcon();

        $this->buildOrPublish();

        $this->builder->postProcess();
    }

    private function buildUnsecure(): void
    {
        $this->builder->preProcess();

        $this->setAppNameAndVersion();

        $this->updateElectronDependencies();

        $this->newLine();
        intro('Copying App to build directory...');
        $this->builder->copyToBuildDirectory();

        $this->newLine();
        intro('Copying latest CA Certificate...');
        $this->builder->copyCertificateAuthority();

        $this->newLine();
        intro('Cleaning .env file...');
        $this->builder->cleanEnvFile();

        $this->newLine();
        intro('Copying app icons...');
        $this->installIcon();

        $this->newLine();
        intro('Pruning vendor directory');
        $this->builder->pruneVendorDirectory();

        $this->buildOrPublish();

        $this->builder->postProcess();
    }

    protected function getEnvironmentVariables(): array
    {
        return array_merge(
            [
                'APP_PATH' => $this->builder->sourcePath(),
                'APP_URL' => config('app.url'),
                'NATIVEPHP_BUILDING' => true,
                'NATIVEPHP_PHP_BINARY_VERSION' => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION,
                'NATIVEPHP_PHP_BINARY_PATH' => $this->builder->phpBinaryPath(),
                'NATIVEPHP_ELECTRON_PATH' => ElectronServiceProvider::electronPath(),
                'NATIVEPHP_BUILD_PATH' => ElectronServiceProvider::buildPath(),
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
                // Azure Trusted Signing
                'AZURE_TENANT_ID' => config('nativephp-internal.azure_trusted_signing.tenant_id'),
                'AZURE_CLIENT_ID' => config('nativephp-internal.azure_trusted_signing.client_id'),
                'AZURE_CLIENT_SECRET' => config('nativephp-internal.azure_trusted_signing.client_secret'),
                'NATIVEPHP_AZURE_PUBLISHER_NAME' => config('nativephp-internal.azure_trusted_signing.publisher_name'),
                'NATIVEPHP_AZURE_ENDPOINT' => config('nativephp-internal.azure_trusted_signing.endpoint'),
                'NATIVEPHP_AZURE_CERTIFICATE_PROFILE_NAME' => config('nativephp-internal.azure_trusted_signing.certificate_profile_name'),
                'NATIVEPHP_AZURE_CODE_SIGNING_ACCOUNT_NAME' => config('nativephp-internal.azure_trusted_signing.code_signing_account_name'),
            ],
            Updater::environmentVariables(),
        );
    }

    private function updateElectronDependencies(): void
    {
        $this->newLine();
        intro('Updating Electron dependencies...');
        Process::path(ElectronServiceProvider::electronPath())
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
        Process::path(ElectronServiceProvider::electronPath())
            ->env($this->getEnvironmentVariables())
            ->forever()
            ->tty(SymfonyProcess::isTtySupported() && ! $this->option('no-interaction'))
            ->run("npm run {$this->buildCommand}:{$this->buildOS}", function (string $type, string $output) {
                echo $output;
            });
    }
}
