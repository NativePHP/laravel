<?php

namespace Native\Desktop\Drivers\Electron;

use Illuminate\Foundation\Application;
use Native\Desktop\Builder\Builder;
use Native\Desktop\Drivers\Electron\Commands\BuildCommand;
use Native\Desktop\Drivers\Electron\Commands\BundleCommand;
use Native\Desktop\Drivers\Electron\Commands\DevelopCommand;
use Native\Desktop\Drivers\Electron\Commands\InstallCommand;
use Native\Desktop\Drivers\Electron\Commands\PublishCommand;
use Native\Desktop\Drivers\Electron\Commands\ResetCommand;
use Native\Desktop\Drivers\Electron\Updater\UpdaterManager;
use Native\Desktop\Support\Composer;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ElectronServiceProvider extends PackageServiceProvider
{
    public static function electronPath(string $path = '')
    {
        // Will use the published electron project, or fallback to the vendor default
        $publishedProjectPath = base_path("nativephp/electron/{$path}");

        return is_dir($publishedProjectPath)
            ? $publishedProjectPath
            : Composer::desktopPackagePath("resources/electron/{$path}");
    }

    public static function buildPath(string $path = '')
    {
        return Composer::desktopPackagePath("resources/build/{$path}");
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('nativephp-electron')
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

        $this->app->bind(Builder::class, function () {
            return Builder::make(
                buildPath: self::buildPath('app')
            );
        });
    }
}
