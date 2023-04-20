<?php

namespace Native\Electron;

use Illuminate\Foundation\Console\ServeCommand;
use Native\Electron\Commands\BuildCommand;
use Native\Electron\Commands\DevelopCommand;
use Native\Electron\Commands\InstallCommand;
use Native\Electron\Commands\QueueWorkerCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ElectronServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('nativephp-electron')
            ->hasCommands([
                InstallCommand::class,
                DevelopCommand::class,
                BuildCommand::class,
                QueueWorkerCommand::class,
            ]);
    }
}
