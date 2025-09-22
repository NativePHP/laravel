<?php

namespace Native\Electron;

use Illuminate\Foundation\Application;
use Native\Electron\Commands\Bifrost\ClearBundleCommand;
use Native\Electron\Commands\Bifrost\DownloadBundleCommand;
use Native\Electron\Commands\Bifrost\InitCommand;
use Native\Electron\Commands\Bifrost\LoginCommand;
use Native\Electron\Commands\Bifrost\LogoutCommand;
use Native\Electron\Commands\BuildCommand;
use Native\Electron\Commands\DevelopCommand;
use Native\Electron\Commands\InstallCommand;
use Native\Electron\Commands\PublishCommand;
use Native\Electron\Commands\ResetCommand;
use Native\Electron\Updater\UpdaterManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ElectronServiceProvider extends PackageServiceProvider
{
    const ELECTRON_PATH = __DIR__.'/../../resources/electron';

    public function configurePackage(Package $package): void
    {
        $package
            ->name('nativephp-electron')
            ->hasCommands([
                InstallCommand::class,
                DevelopCommand::class,
                BuildCommand::class,
                PublishCommand::class,
                ResetCommand::class,
                LoginCommand::class,
                LogoutCommand::class,
                InitCommand::class,
                DownloadBundleCommand::class,
                ClearBundleCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->bind('nativephp.updater', function (Application $app) {
            return new UpdaterManager($app);
        });
    }

    protected function getPackageBaseDir(): string
    {
        return dirname(parent::getPackageBaseDir());
    }
}
