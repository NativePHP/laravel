<?php

namespace Native\Desktop\Drivers\Electron\Commands;

use Illuminate\Console\Command;
use Native\Desktop\Drivers\Electron\Traits\Installer;
use Native\Desktop\Support\Composer;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;

#[AsCommand(
    name: 'native:install',
    description: 'Install all of the NativePHP resources',
)]
class InstallCommand extends Command
{
    use Installer;

    protected $signature = 'native:install
        {--force : Overwrite existing files by default}
        {--installer=npm : The package installer to use: npm, yarn or pnpm}';

    public function handle(): void
    {
        intro('Publishing NativePHP Service Provider...');

        $withoutInteraction = $this->option('no-interaction');

        $this->call('vendor:publish', ['--tag' => 'nativephp-provider']);
        $this->call('vendor:publish', ['--tag' => 'nativephp-config']);

        Composer::installScripts();

        $installer = $this->getInstaller($this->option('installer'));

        $this->installNPMDependencies(
            force: $this->option('force'),
            installer: $installer,
            withoutInteraction: $withoutInteraction
        );

        $shouldPromptForServe = ! $withoutInteraction && ! $this->option('force');

        if ($shouldPromptForServe && confirm('Would you like to start the NativePHP development server', false)) {
            $this->call('native:serve', [
                '--installer' => $installer,
                '--no-dependencies',
                '--no-interaction' => $withoutInteraction,
            ]);
        }

        outro('NativePHP scaffolding installed successfully.');
    }
}
