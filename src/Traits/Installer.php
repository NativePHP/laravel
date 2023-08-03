<?php

namespace Native\Electron\Traits;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;

trait Installer
{
    use ExecuteCommand;

    protected function installNPMDependencies(bool $force, ?string $installer = 'npm'): void
    {
        if ($force || confirm('Would you like to install the NativePHP NPM dependencies?', true)) {
            note('Installing NPM dependencies (This may take a while)...');

            if (! $installer) {
                $this->installDependencies();
            } else {
                $this->installDependencies(installer: $installer);
            }
            $this->output->newLine();
        }
    }

    protected function installDependencies(?string $installer): void
    {
        [$installer, $command] = $this->getInstallerAndCommand(installer: $installer);

        note("Installing NPM dependencies using the {$installer} package manager...");
        $this->executeCommand(command: $command);
    }

    protected function getInstallerAndCommand(?string $installer, $type = 'install'): array
    {
        $commands = $this->getCommandArrays(type: $type);
        $installer = $this->getInstaller(installer: $installer);

        return [$installer, $commands[$installer]];
    }

    protected function getInstaller(string $installer)
    {
        if (! array_key_exists($installer, $this->getCommandArrays())) {
            error("Invalid installer ** {$installer} ** provided.");
            $keys = array_keys($this->getCommandArrays());
            $techs = implode(', ', $keys);
            $installer = select('Choose one of the following installers: '.$techs, $keys, 0);
        }

        return $installer;
    }
}
