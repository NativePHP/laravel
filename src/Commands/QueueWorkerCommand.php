<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

use function Laravel\Prompts\intro;

class QueueWorkerCommand extends Command
{
    protected $signature = 'native:queue {--port=4000}';

    protected function getAppDirectory(): string|false
    {
        $appName = Str::slug(config('app.name'));

        if (PHP_OS_FAMILY === 'Windows') {
            $basePath = getenv('APPDATA');
        } elseif (PHP_OS_FAMILY === 'Linux') {
            $basePath = getenv('XDG_CONFIG_HOME') ?: getenv('HOME').'/.config';
        } elseif (PHP_OS_FAMILY === 'Darwin') {
            $basePath = getenv('HOME').'/Library/Application Support';
        }

        return realpath($basePath.'/'.$appName);
    }

    public function handle(): void
    {
        intro('Starting NativePHP queue worker…');

        $phpBinary = __DIR__.'/../../resources/js/resources/php/php';

        Process::path(base_path())
            ->env([
                'APP_PATH' => base_path(),
                'NATIVEPHP_RUNNING' => true,
                'NATIVEPHP_STORAGE_PATH' => $this->getAppDirectory().'/storage',
                'NATIVEPHP_API_URL' => 'http://localhost:'.$this->option('port').'/api/',
                'NATIVEPHP_DATABASE_PATH' => $this->getAppDirectory().'/database/database.sqlite',
            ])
            ->forever()
            ->tty()
            ->run($phpBinary.' artisan queue:work', function (string $type, string $output) {
                echo $output;
            });
    }
}
