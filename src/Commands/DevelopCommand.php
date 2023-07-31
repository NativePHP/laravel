<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Native\Electron\Concerns\LocatesPhpBinary;

class DevelopCommand extends Command
{
    use LocatesPhpBinary;

    protected $signature = 'native:serve {--no-queue} {--D|no-dependencies}';

    public function handle()
    {
        $this->info('Starting NativePHP dev server…');

        $this->info('Fetching latest dependencies…');

        if (! $this->option('no-dependencies')) {
            Process::path(__DIR__.'/../../resources/js/')
                ->env([
                    'NATIVEPHP_PHP_BINARY_PATH' => base_path($this->phpBinaryPath()),
                    'NATIVEPHP_CERTIFICATE_FILE_PATH' => base_path($this->binaryPackageDirectory().'cacert.pem'),
                ])
                ->forever()
                ->run('npm install', function (string $type, string $output) {
                    if ($this->getOutput()->isVerbose()) {
                        echo $output;
                    }
                });
        }

        $this->info('Starting NativePHP app…');

        if (PHP_OS_FAMILY === 'Darwin') {
            $this->patchPlist();
        }

        Process::path(__DIR__.'/../../resources/js/')
            ->env([
                'APP_PATH' => base_path(),
                'NATIVEPHP_PHP_BINARY_PATH' => base_path($this->phpBinaryPath()),
                'NATIVEPHP_CERTIFICATE_FILE_PATH' => base_path($this->binaryPackageDirectory().'cacert.pem'),
                'NATIVE_PHP_SKIP_QUEUE' => $this->option('no-queue') ? true : false,
            ])
            ->forever()
            ->tty(PHP_OS_FAMILY != 'Windows')
            ->run('npm run dev', function (string $type, string $output) {
                if ($this->getOutput()->isVerbose()) {
                    echo $output;
                }
            });
    }

    /**
     * Patch Electron's Info.plist to show the correct app name
     * during development.
     *
     * @return void
     */
    protected function patchPlist()
    {
        $pList = file_get_contents(__DIR__.'/../../resources/js/node_modules/electron/dist/Electron.app/Contents/Info.plist');

        // Change the CFBundleName to the correct app name
        $pattern = '/(<key>CFBundleName<\/key>\s+<string>)(.*?)(<\/string>)/m';
        $pList = preg_replace($pattern, '$1'.config('app.name').'$3', $pList);

        $pattern = '/(<key>CFBundleDisplayName<\/key>\s+<string>)(.*?)(<\/string>)/m';
        $pList = preg_replace($pattern, '$1'.config('app.name').'$3', $pList);

        file_put_contents(__DIR__.'/../../resources/js/node_modules/electron/dist/Electron.app/Contents/Info.plist', $pList);
    }
}
