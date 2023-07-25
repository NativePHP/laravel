<?php

namespace Native\Electron\Traits;

trait Installer
{
    protected function installNPMDependencies($installer = 'npm')
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

    protected function installDependencies(?string $installer = null)
    {
        $installers = [
            'npm'  => 'npm set progress=false && npm install',
            'yarn' => 'yarn',
            'pnpm' => 'pnpm install',
        ];

        if (!array_key_exists($installer, $installers))) {
            $this->error("Invalid installer {$installer} provided.");
            $keys = array_keys($installers);
            $techs = implode(', ', $keys);
            $installer = $this->choice('Choose one of the following installers: '. $techs, $keys);
        }

        $this->info("Installing {$installer} dependencies using the npm package manager...");
        $this->executeCommand("{$installers[$installer]}", $this->nativePhpPath());
    }
}
