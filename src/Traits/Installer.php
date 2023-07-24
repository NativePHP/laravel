<?php

namespace Native\Electron\Traits;

trait Installer
{
    protected function installDependencies()
    {
        if ($this->option('force') || $this->confirm('Would you like to install the NativePHP NPM dependencies?', true)) {
            $this->comment('Installing NPM dependencies (This may take a while)...');

            switch ($this->option('installer')) {
                case 'yarn':
                    $this->info('Installing NPM dependencies using the yarn package manager...');
                    $this->installYarnDependencies();
                    break;
                case 'pnpm':
                    $this->info('Installing NPM dependencies using the pnpm package manager...');
                    $this->installPnpmDependencies();
                    break;
                default:
                    $this->info('Installing NPM dependencies using the npm package manager...');
                    $this->installNpmDependencies();
            }

            $this->output->newLine();
        }
    }

    protected function installNpmDependencies()
    {
        $this->executeCommand('npm set progress=false && npm install', $this->nativePhpPath());
    }

    protected function installYarnDependencies()
    {
        $this->executeCommand('yarn install', $this->nativePhpPath());
    }

    protected function installPnpmDependencies()
    {
        $this->executeCommand('pnpm install', $this->nativePhpPath());
    }
}
