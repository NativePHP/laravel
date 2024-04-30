<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use Native\Electron\Traits\Installer;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;

class InstallCommand extends Command
{
    use Installer;

    protected $signature = 'native:install {--force : Overwrite existing files by default} {--installer=npm : The package installer to use: npm, yarn or pnpm}';

    protected $description = 'Install all of the NativePHP resources';

    public function handle(): void
    {
        intro('Publishing NativePHP Service Provider...');

        $withoutInteraction = $this->option('no-interaction');

        $this->call('vendor:publish', ['--tag' => 'nativephp-provider']);
        $this->call('vendor:publish', ['--tag' => 'nativephp-config']);

        $installer = $this->getInstaller($this->option('installer'), $withoutInteraction);

        $this->installNPMDependencies(force: $this->option('force'), installer: $installer, withoutInteraction: $withoutInteraction);

        $shouldPromptForServe = ! $withoutInteraction && ! $this->option('force');

        if ($shouldPromptForServe && confirm('Would you like to start the NativePHP development server', false)) {
            $this->call('native:serve', ['--installer' => $installer]);
        }

        outro('NativePHP scaffolding installed successfully.');
    }
}
