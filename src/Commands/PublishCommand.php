<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Native\Electron\Facades\Updater;

class PublishCommand extends Command
{
    protected $signature = 'native:publish {os=mac}';

    public function handle()
    {
        $this->info('Building and publishing NativePHP app…');

        Process::path(__DIR__.'/../../resources/js/')
            ->env($this->getEnvironmentVariables())
            ->run('npm update', function (string $type, string $output) {
                echo $output;
            });

        Process::path(base_path())
            ->run('composer install --no-dev', function (string $type, string $output) {
                echo $output;
            });

        $updaterConnection = config('nativephp.updater.default');
        $updaterConfig = config("nativephp.updater.connections.{$updaterConnection}", []);

        Process::path(__DIR__.'/../../resources/js/')
            ->env($this->getEnvironmentVariables())
            ->forever()
            ->tty()
            ->run('npm run publish:mac-arm', function (string $type, string $output) {
                echo $output;
            });
    }

    protected function getEnvironmentVariables()
    {
        return array_merge(
            [
                'APP_PATH' => base_path(),
                'NATIVEPHP_BUILDING' => true,
                'NATIVEPHP_PHP_BINARY_PATH' => base_path('vendor/nativephp/php-bin/bin/mac'),
                'NATIVEPHP_CERTIFICATE_FILE_PATH' => base_path('vendor/nativephp/php-bin/cacert.pem'),
                'NATIVEPHP_APP_NAME' => config('app.name'),
                'NATIVEPHP_APP_ID' => config('nativephp.app_id'),
                'NATIVEPHP_APP_VERSION' => config('nativephp.version'),
                'NATIVEPHP_APP_FILENAME' => Str::slug(config('app.name')),
                'NATIVEPHP_UPDATER_CONFIG' => json_encode(Updater::builderOptions()),
            ],
            Updater::environmentVariables(),
        );
    }
}
