<?php

namespace Native\Electron\Traits;

trait Installer
{
    use ExecuteCommand;

    protected function installNPMDependencies(bool $force, ?string $installer = 'npm'): void
    {
        if ($force || $this->confirm('Would you like to install the NativePHP NPM dependencies?', true)) {
            $this->comment('Installing NPM dependencies (This may take a while)...');

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

        $this->info("Installing NPM dependencies using the {$installer} package manager...");
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
        $installers = $this->getCommandArrays();

        if (! array_key_exists($installer, $this->getCommandArrays())) {
            $this->error("Invalid installer ** {$installer} ** provided.");
            $keys = array_keys($this->getCommandArrays());
            $techs = implode(', ', $keys);
            $installer = $this->choice('Choose one of the following installers: '.$techs, $keys, 0);
        }

        return $installer;
    }
}
