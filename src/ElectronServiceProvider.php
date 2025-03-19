<?php

namespace Native\Electron;

use Illuminate\Foundation\Application;
use Native\Electron\Commands\BuildCommand;
use Native\Electron\Commands\BundleCommand;
use Native\Electron\Commands\DevelopCommand;
use Native\Electron\Commands\InstallCommand;
use Native\Electron\Commands\PublishCommand;
use Native\Electron\Commands\ResetCommand;
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
                BundleCommand::class,
                ResetCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->bind('nativephp.updater', function (Application $app) {
            return new UpdaterManager($app);
        });
    }
}
