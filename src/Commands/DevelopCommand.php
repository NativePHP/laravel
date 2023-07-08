<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class DevelopCommand extends Command
{
    protected $signature = 'native:serve {--no-queue}';

    public function handle()
    {
        $this->info('Starting NativePHP dev server…');

        $this->info('Fetching latest dependencies…');

        Process::path(__DIR__.'/../../resources/js/')
            ->env([
                'NATIVEPHP_PHP_BINARY_PATH' => base_path('vendor/nativephp/php-bin/bin/mac'),
                'NATIVEPHP_CERTIFICATE_FILE_PATH' => base_path('vendor/nativephp/php-bin/cacert.pem'),
            ])
            ->forever()
            ->run('npm update', function (string $type, string $output) {
                if ($this->getOutput()->isVerbose()) {
                    echo $output;
                }
            });
        $this->info('Starting NativePHP app…');

        Process::path(__DIR__.'/../../resources/js/')
            ->env([
                'APP_PATH' => base_path(),
                'NATIVEPHP_PHP_BINARY_PATH' => base_path('vendor/nativephp/php-bin/bin/mac'),
                'NATIVEPHP_CERTIFICATE_FILE_PATH' => base_path('vendor/nativephp/php-bin/cacert.pem'),
                'NATIVE_PHP_SKIP_QUEUE' => $this->option('no-queue') ? true : false,
            ])
            ->forever()
            ->tty()
            ->run('npm run dev', function (string $type, string $output) {
                if ($this->getOutput()->isVerbose()) {
                    echo $output;
                }
            });
    }
}
