<?php

namespace Native\Laravel;

use Illuminate\Support\Arr;
use Native\Laravel\Commands\LoadPHPConfigurationCommand;
use Native\Laravel\Commands\LoadStartupConfigurationCommand;
use Native\Laravel\Commands\MigrateCommand;
use Native\Laravel\Commands\MinifyApplicationCommand;
use Native\Laravel\Logging\LogWatcher;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NativeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('nativephp')
            ->hasCommands([
                MigrateCommand::class,
                MinifyApplicationCommand::class,
                LoadStartupConfigurationCommand::class,
                LoadPHPConfigurationCommand::class,
            ])
            ->hasConfigFile()
            ->hasRoute('api')
            ->publishesServiceProvider('NativeAppServiceProvider');
    }

    public function packageRegistered()
    {
        $this->mergeConfigFrom($this->package->basePath('/../config/nativephp-internal.php'), 'nativephp-internal');

        $this->app->singleton(MigrateCommand::class, function ($app) {
            return new MigrateCommand($app['migrator'], $app['events']);
        });

        if (config('nativephp-internal.running')) {
            $this->configureApp();
        }
    }

    protected function configureApp()
    {
        if (config('app.debug')) {
            app(LogWatcher::class)->register();
        }

        $this->rewriteStoragePath();

        $this->rewriteDatabase();

        config(['session.driver' => 'file']);
        config(['queue.default' => 'database']);
    }

    protected function rewriteStoragePath()
    {
        if (config('app.debug')) {
            return;
        }

        $oldStoragePath = $this->app->storagePath();

        $this->app->useStoragePath(config('nativephp-internal.storage_path'));

        // Patch all config values that contain the old storage path
        $config = Arr::dot(config()->all());

        foreach ($config as $key => $value) {
            if (is_string($value) && str_contains($value, $oldStoragePath)) {
                $newValue = str_replace($oldStoragePath, config('nativephp-internal.storage_path'), $value);
                config([$key => $newValue]);
            }
        }
    }

    public function rewriteDatabase()
    {
        $databasePath = config('nativephp-internal.database_path');

        if (config('app.debug')) {
            $databasePath = database_path('nativephp.sqlite');

            if (! file_exists($databasePath)) {
                touch($databasePath);
            }
        }

        config(['database.connections.nativephp' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => $databasePath,
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ]]);

        config(['database.default' => 'nativephp']);
    }
}
