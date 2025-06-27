<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use Native\Electron\Traits\Installer;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
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

        $this->installComposerScript();

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

    private function installComposerScript()
    {
        info('Installing `composer native:dev` script alias...');

        $composer = json_decode(file_get_contents(base_path('composer.json')));
        throw_unless($composer, RuntimeException::class, "composer.json couldn't be parsed");

        $composerScripts = $composer->scripts ?? (object) [];

        if ($composerScripts->{'native:dev'} ?? false) {
            note('native:dev script already installed... skipping.');

            return;
        }

        $composerScripts->{'native:dev'} = [
            'Composer\\Config::disableProcessTimeout',
            'npx concurrently -k -c "#93c5fd,#c4b5fd" "php artisan native:serve --no-interaction" "npm run dev" --names=app,vite',
        ];

        data_set($composer, 'scripts', $composerScripts);

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL
        );

        note('native:dev script installed!');
    }
}
