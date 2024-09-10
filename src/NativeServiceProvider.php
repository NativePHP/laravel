<?php

namespace Native\Laravel;

use Illuminate\Console\Application as Artisan;
use Illuminate\Support\Arr;
use Native\Laravel\Commands\FreshCommand;
use Native\Laravel\Commands\LoadPHPConfigurationCommand;
use Native\Laravel\Commands\LoadStartupConfigurationCommand;
use Native\Laravel\Commands\MigrateCommand;
use Native\Laravel\Commands\MinifyApplicationCommand;
use Native\Laravel\Commands\SeedDatabaseCommand;
use Native\Laravel\Events\EventWatcher;
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
                FreshCommand::class,
                SeedDatabaseCommand::class,
                MinifyApplicationCommand::class,
            ])
            ->hasConfigFile()
            ->hasRoute('api')
            ->publishesServiceProvider('NativeAppServiceProvider');
    }

    public function packageRegistered()
    {
        $this->mergeConfigFrom($this->package->basePath('/../config/nativephp-internal.php'), 'nativephp-internal');

        $this->app->singleton(FreshCommand::class, function ($app) {
            return new FreshCommand($app['migrator']);
        });

        $this->app->singleton(MigrateCommand::class, function ($app) {
            return new MigrateCommand($app['migrator'], $app['events']);
        });

        if (config('nativephp-internal.running')) {
            Artisan::starting(function ($artisan) {
                $artisan->resolveCommands([
                    LoadStartupConfigurationCommand::class,
                    LoadPHPConfigurationCommand::class,
                ]);
            });

            $this->configureApp();
        }
    }

    protected function configureApp()
    {
        if (config('app.debug')) {
            app(LogWatcher::class)->register();
        }

        app(EventWatcher::class)->register();

        $this->rewriteStoragePath();

        $this->rewriteDatabase();

        $this->configureDisks();

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

    public function removeDatabase()
    {
        $databasePath = config('nativephp-internal.database_path');

        if (config('app.debug')) {
            $databasePath = database_path('nativephp.sqlite');

            if (! file_exists($databasePath)) {
                return;
            }
        }

        unlink($databasePath);
    }

    protected function configureDisks(): void
    {
        $disks = [
            'NATIVEPHP_USER_HOME_PATH' => 'user_home',
            'NATIVEPHP_APP_DATA_PATH' => 'app_data',
            'NATIVEPHP_USER_DATA_PATH' => 'user_data',
            'NATIVEPHP_DESKTOP_PATH' => 'desktop',
            'NATIVEPHP_DOCUMENTS_PATH' => 'documents',
            'NATIVEPHP_DOWNLOADS_PATH' => 'downloads',
            'NATIVEPHP_MUSIC_PATH' => 'music',
            'NATIVEPHP_PICTURES_PATH' => 'pictures',
            'NATIVEPHP_VIDEOS_PATH' => 'videos',
            'NATIVEPHP_RECENT_PATH' => 'recent',
        ];

        foreach ($disks as $env => $disk) {
            if (! env($env)) {
                continue;
            }

            config(['filesystems.disks.'.$disk => [
                'driver' => 'local',
                'root' => env($env, ''),
                'throw' => false,
            ]]);
        }
    }
}
