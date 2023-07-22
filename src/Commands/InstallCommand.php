<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    protected $signature = 'native:install {--force : Overwrite existing files by default}';

    protected $description = 'Install all of the NativePHP resources';

    public function handle()
    {
        $this->comment('Publishing NativePHP Service Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'nativephp-provider']);
        $this->callSilent('vendor:publish', ['--tag' => 'nativephp-config']);

        if ($this->option('force') || $this->confirm('Would you like to install the NativePHP NPM dependencies?', true)) {
            $this->installNpmDependencies();

            $this->output->newLine();
        }

        if (! $this->option('force') && $this->confirm('Would you like to start the NativePHP development server', false)) {
            $this->call('native:serve');
        }

        $this->info('NativePHP scaffolding installed successfully.');
    }

    protected function nativePhpPath()
    {
        return realpath(__DIR__.'/../../resources/js');
    }

    protected function installNpmDependencies()
    {
        $this->executeCommand('npm set progress=false && npm install', $this->nativePhpPath());
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
