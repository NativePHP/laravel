<?php

namespace Native\Electron\Commands;

use Illuminate\Console\Command;
// use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Process;

class InstallCommand extends Command
{
    protected $signature = 'native:install {--force : Overwrite existing files by default}';

    protected $description = 'Install all of the NativePHP resources';

    public function handle()
    {
        // dd(config('nativephp.binary_package'));
        $this->comment('Publishing NativePHP Service Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'nativephp-provider']);

        if ($this->option('force') || $this->confirm('Would you like to install the NativePHP NPM dependencies?', true)) {
            $this->installNpmDependencies();

            $this->output->newLine();
        }

        if (! $this->option('force') && $this->confirm('Would you like to start the NativePHP development server', false)) {
            $this->call('native:serve');
        }

        $this->info('NativePHP scaffolding installed successfully.');
    }

    protected function nativePhpPath()
    {
        return realpath(__DIR__.'/../../resources/js');
    }

    protected function installNpmDependencies()
    {
        // $this->executeCommand('npm set progress=false && npm install', $this->nativePhpPath());
        // $this->executeCommand('npm install', $this->nativePhpPath());
        $phpBinPackageDir = config('nativephp.binary_package');
        $nativeBinaryPath = $phpBinPackageDir . 'bin/' . (PHP_OS_FAMILY === 'Windows' ? 'win' : 'mac');
        $this->info("NativePHP binary path: $nativeBinaryPath");
        $this->info('Fetching latest dependencies…');
        Process::path(__DIR__ . '/../../resources/js/')
                ->env([
                    'NATIVEPHP_PHP_BINARY_PATH' => base_path($nativeBinaryPath),
                    'NATIVEPHP_CERTIFICATE_FILE_PATH' => base_path($phpBinPackageDir . 'cacert.pem'),
                ])
                ->forever()
                ->run('npm install', function (string $type, string $output) {
                    // if ($this->getOutput()->isVerbose()) {
                        echo $output;
                    // }
                });
    }

    // protected function executeCommand($command, $path)
    // {
    //     $process = (Process::fromShellCommandline($command, $path))->setTimeout(null);

    //     if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
    //         $process->setTty(true);
    //     }

    //     $process->run(function ($type, $line) {
    //         $this->output->write($line);
    //     });
    // }
}
