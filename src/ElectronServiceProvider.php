<?php

namespace Native\Electron;

use Native\Electron\Commands\BuildCommand;
use Native\Electron\Commands\DevelopCommand;
use Native\Electron\Commands\InstallCommand;
use Native\Electron\Commands\PublishCommand;
use Native\Electron\Commands\QueueWorkerCommand;
use Native\Electron\Updater\UpdaterManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ElectronServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('nativephp-electron')
            ->hasConfigFile('nativephp')
            ->hasCommands([
                InstallCommand::class,
                DevelopCommand::class,
                BuildCommand::class,
                PublishCommand::class,
                QueueWorkerCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('nativephp.updater', function ($app) {
            return new UpdaterManager($app);
        });
    }
}
