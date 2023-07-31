<?php

namespace Native\Electron\Traits;

use Native\Electron\Concerns\LocatesPhpBinary;
use Illuminate\Support\Facades\Process;

trait Installer
{
    use LocatesPhpBinary;

    protected function installNPMDependencies(?string $installer = 'npm'): void
    {
        if ($this->option('force') || $this->confirm('Would you like to install the NativePHP NPM dependencies?', true)) {
            $this->comment('Installing NPM dependencies (This may take a while)...');

            if (!$installer) {
                $this->installDependencies();
            } else {
                $this->installDependencies(installer: $installer);
            }

            $this->output->newLine();
        }
    }

    protected function installDependencies(?string $installer = null): void
    {
        $installers = [
            'npm'  => 'npm install',
            'yarn' => 'yarn',
            'pnpm' => 'pnpm install',
        ];

        if (!array_key_exists($installer, $installers)) {
            $this->error("Invalid installer ** {$installer} ** provided.");
            $keys = array_keys($installers);
            $techs = implode(', ', $keys);
            $installer = $this->choice('Choose one of the following installers: ' . $techs, $keys, 0);
        }

        $this->info("Installing NPM dependencies using the {$installer} package manager...");
        $this->executeCommand(command: $installers[$installer]);
    }

    protected function nativePhpPath(): string
    {
        return realpath(__DIR__ . '/../../resources/js');
    }

    protected function executeCommand(string $command): void
    {
        $this->info('Fetching latest dependencies…');
        Process::path(__DIR__ . '/../../resources/js/')
            ->env([
                'NATIVEPHP_PHP_BINARY_PATH'       => base_path($this->phpBinaryPath()),
                'NATIVEPHP_CERTIFICATE_FILE_PATH' => base_path($this->binaryPackageDirectory() . 'cacert.pem'),
            ])
            ->forever()
            ->tty(PHP_OS_FAMILY != 'Windows')
            ->run($command, function (string $type, string $output) {
                if ($this->getOutput()->isVerbose()) {
                    echo $output;
                }
            });
    }
}
