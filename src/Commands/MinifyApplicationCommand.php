<?php

namespace Native\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Native\Laravel\Compactor\Php;
use Symfony\Component\Finder\Finder;

class MinifyApplicationCommand extends Command
{
    protected $signature = 'native:minify {app}';

    public function handle()
    {
        $appPath = realpath($this->argument('app'));

        if (! is_dir($appPath)) {
            $this->error('The app path is not a directory');

            return;
        }

        $this->info('Minifying application…');

        $this->cleanUpEnvFile($appPath);

        $compactor = new Php();

        $phpFiles = Finder::create()
            ->files()
            ->name('*.php')
            ->in($appPath);

        foreach ($phpFiles as $phpFile) {
            $minifiedContent = $compactor->compact($phpFile->getRealPath(), $phpFile->getContents());
            file_put_contents($phpFile->getRealPath(), $minifiedContent);
        }
    }

    protected function cleanUpEnvFile(string $appPath): void
    {
        $envFile = $appPath.'/.env';

        if (! file_exists($envFile)) {
            return;
        }

        $this->info('Cleaning up .env file…');

        $cleanUpKeys = config('nativephp.cleanup_env_keys', []);

        $envContent = file_get_contents($envFile);
        $envValues = collect(explode("\n", $envContent))
            ->filter(function (string $line) use ($cleanUpKeys) {
                $key = Str::before($line, '=');

                return ! Str::is($cleanUpKeys, $key);
            })
            ->join("\n");

        file_put_contents($envFile, $envValues);
    }
}
