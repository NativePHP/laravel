<?php

namespace Native\Electron\Traits;

trait Installer
{
    protected function installNPMDependencies()
    {
        if ($this->option('force') || $this->confirm('Would you like to install the NativePHP NPM dependencies?', true)) {
            $this->comment('Installing NPM dependencies (This may take a while)...');

            if (!this->option('installer')) {
                $this->info('Installing NPM dependencies using the npm package manager...');
                $this->installWithNPM();
            }

            switch ($this->option('installer')) {
                case 'yarn':
                    $this->info('Installing NPM dependencies using the yarn package manager...');
                    $this->installWithYarn();
                    break;
                case 'pnpm':
                    $this->info('Installing NPM dependencies using the pnpm package manager...');
                    $this->installWithPNPM();
                    break;
                default:
                    $this->info('Installing NPM dependencies using the npm package manager...');
                    $this->installWithNPM();
            }

            $this->output->newLine();
        }
    }

    protected function installWithNPM()
    {
        $this->executeCommand('npm set progress=false && npm install', $this->nativePhpPath());
    }

    protected function installWithYarn()
    {
        $this->executeCommand('yarn install', $this->nativePhpPath());
    }

    protected function installWithPNPM()
    {
        $this->executeCommand('pnpm install', $this->nativePhpPath());
    }
}
