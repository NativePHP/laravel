<?php

namespace Native\Laravel\Commands;

use Illuminate\Console\Command;
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

        $compactor = new \Native\Laravel\Compactor\Php();

        $phpFiles = Finder::create()
            ->files()
            ->name('*.php')
            ->in($appPath);

        foreach ($phpFiles as $phpFile) {
            $minifiedContent = $compactor->compact($phpFile->getRealPath(), $phpFile->getContents());
            file_put_contents($phpFile->getRealPath(), $minifiedContent);
        }
    }
}
