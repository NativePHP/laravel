<?php

namespace Native\Electron\Traits;

use Symfony\Component\Process\Process;

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
        $this->executeCommand("{$installers[$installer]}", $this->nativePhpPath());
    }

    protected function nativePhpPath()
    {
        return realpath(__DIR__ . '/../../resources/js');
    }

    protected function executeCommand($command, $path)
    {
        $process = (Process::fromShellCommandline($command, $path))->setTimeout(null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run(function ($type, $line) {
            $this->output->write($line);
        });
    }
}
