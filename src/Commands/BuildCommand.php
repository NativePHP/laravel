<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Native\Electron\Concerns\LocatesPhpBinary;
use Native\Electron\Facades\Updater;

class BuildCommand extends Command
{
    use LocatesPhpBinary;

    protected $signature = 'native:build {os=all : The operating system to build for (all, linux, mac, windows)}';

    public function handle()
    {
        $this->info('Build NativePHP app…');

        Process::path(base_path())
            ->run('composer install --no-dev', function (string $type, string $output) {
                echo $output;
            });

        $buildCommand = 'npm run build';
        if ($this->argument('os')) {
            $buildCommand .= ':'.$this->argument('os');
        }

        Process::path(__DIR__.'/../../resources/js/')
            ->env($this->getEnvironmentVariables())
            ->forever()
            ->tty(PHP_OS_FAMILY != 'Windows')
            ->run($buildCommand, function (string $type, string $output) {
                echo $output;
            });
    }

    protected function getEnvironmentVariables()
    {
        return array_merge(
            [
                'APP_PATH' => base_path(),
                'APP_URL' => config('app.url'),
                'NATIVEPHP_BUILDING' => true,
                'NATIVEPHP_PHP_BINARY_PATH' => base_path($this->phpBinaryPath()),
                'NATIVEPHP_CERTIFICATE_FILE_PATH' => base_path($this->binaryPackageDirectory().'cacert.pem'),
                'NATIVEPHP_APP_NAME' => config('app.name'),
                'NATIVEPHP_APP_ID' => config('nativephp.app_id'),
                'NATIVEPHP_APP_VERSION' => config('nativephp.version'),
                'NATIVEPHP_APP_FILENAME' => Str::slug(config('app.name')),
                'NATIVEPHP_APP_AUTHOR' => config('nativephp.author'),
                'NATIVEPHP_UPDATER_CONFIG' => json_encode(Updater::builderOptions()),
            ],
            Updater::environmentVariables(),
        );
    }
}
