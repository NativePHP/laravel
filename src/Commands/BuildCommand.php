<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class BuildCommand extends Command
{
    protected $signature = 'native:build';

    public function handle()
    {
        $this->info('Build NativePHP app…');

        Process::path(__DIR__.'/../../resources/js/')
            ->run('yarn', function (string $type, string $output) {
                echo $output;
            });

        Process::path(base_path())
            ->run('composer install --no-dev', function (string $type, string $output) {
                echo $output;
            });

        Process::path(__DIR__.'/../../resources/js/')
            ->env([
                'APP_PATH' => base_path(),
                'NATIVEPHP_BUILDING' => true,
                'NATIVEPHP_PHP_BINARY' => PHP_BINARY,
                'NATIVEPHP_APP_NAME' => config('app.name'),
                'NATIVEPHP_APP_FILENAME' => Str::slug(config('app.name')),
            ])
            ->forever()
            ->tty()
            ->run('yarn build:mac', function (string $type, string $output) {
                echo $output;
            });
    }
}
