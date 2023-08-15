<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use Native\Electron\Traits\Installer;

class InstallCommand extends Command
{
    use Installer;

    protected $signature = 'native:install {--force : Overwrite existing files by default} {--installer=npm : The package installer to use: npm, yarn or pnpm} {--ci : Whether the build is running in a CI environment}';

    protected $description = 'Install all of the NativePHP resources';

    public function handle(): void
    {
        intro('Publishing NativePHP Service Provider...');

        $isCI = $this->option('ci');

        $this->callSilent('vendor:publish', ['--tag' => 'nativephp-provider']);
        $this->callSilent('vendor:publish', ['--tag' => 'nativephp-config']);

        $installer = $this->getInstaller($this->option('installer'), $isCI);

        $this->installNPMDependencies(force: $this->option('force'), installer: $installer, isCI: $isCI);

        $shouldPromptForServe = ! $isCI && ! $this->option('force');

        if ($shouldPromptForServe && confirm('Would you like to start the NativePHP development server', false)) {
            $this->call('native:serve', ['--installer' => $installer]);
        }

        outro('NativePHP scaffolding installed successfully.');
    }
}
