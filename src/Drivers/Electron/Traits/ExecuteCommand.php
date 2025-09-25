<?php

namespace Native\Desktop\Drivers\Electron\Traits;

use Illuminate\Support\Facades\Process;
use Native\Desktop\Builder\Builder;
use Native\Desktop\Drivers\Electron\ElectronServiceProvider;

use function Laravel\Prompts\note;

trait ExecuteCommand
{
    protected function executeCommand(
        string $command,
        bool $skip_queue = false,
        string $type = 'install',
        bool $withoutInteraction = false
    ): void {

        $builder = resolve(Builder::class);

        $envs = [
            'install' => [
                'NATIVEPHP_PHP_BINARY_VERSION' => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION,
                'NATIVEPHP_PHP_BINARY_PATH' => $builder->phpBinaryPath(),
            ],
            'serve' => [
                'APP_PATH' => base_path(),
                'NATIVEPHP_PHP_BINARY_VERSION' => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION,
                'NATIVEPHP_PHP_BINARY_PATH' => $builder->phpBinaryPath(),
                'NATIVE_PHP_SKIP_QUEUE' => $skip_queue,
                'NATIVEPHP_BUILDING' => false,
            ],
        ];

        note('Fetching latest dependencies…');

        Process::path(ElectronServiceProvider::ELECTRON_PATH)
            ->env($envs[$type])
            ->forever()
            ->tty(! $withoutInteraction && PHP_OS_FAMILY != 'Windows')
            ->run($command, function (string $type, string $output) {
                if ($this->getOutput()->isVerbose()) {
                    echo $output;
                }
            });
    }

    protected function getCommandArrays(string $type = 'install'): array
    {
        $commands = [
            'install' => [
                'npm' => 'npm install',
                'yarn' => 'yarn',
                'pnpm' => 'pnpm install',
            ],
            'dev' => [
                'npm' => 'npm run dev',
                'yarn' => 'yarn dev',
                'pnpm' => 'pnpm run dev',
            ],
        ];

        return $commands[$type];
    }
}
