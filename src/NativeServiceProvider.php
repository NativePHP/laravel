<?php

namespace Native\Laravel;

use Illuminate\Console\Application;
use Illuminate\Foundation\Application as Foundation;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Native\Laravel\ChildProcess as ChildProcessImplementation;
use Native\Laravel\Commands\DebugCommand;
use Native\Laravel\Commands\FreshCommand;
use Native\Laravel\Commands\LoadPHPConfigurationCommand;
use Native\Laravel\Commands\LoadStartupConfigurationCommand;
use Native\Laravel\Commands\MigrateCommand;
use Native\Laravel\Commands\SeedDatabaseCommand;
use Native\Laravel\Commands\WipeDatabaseCommand;
use Native\Laravel\Contracts\ChildProcess as ChildProcessContract;
use Native\Laravel\Contracts\GlobalShortcut as GlobalShortcutContract;
use Native\Laravel\Contracts\PowerMonitor as PowerMonitorContract;
use Native\Laravel\Contracts\QueueWorker as QueueWorkerContract;
use Native\Laravel\Contracts\WindowManager as WindowManagerContract;
use Native\Laravel\DTOs\QueueConfig;
use Native\Laravel\Events\EventWatcher;
use Native\Laravel\Exceptions\Handler;
use Native\Laravel\GlobalShortcut as GlobalShortcutImplementation;
use Native\Laravel\Http\Middleware\PreventRegularBrowserAccess;
use Native\Laravel\Logging\LogWatcher;
use Native\Laravel\PowerMonitor as PowerMonitorImplementation;
use Native\Laravel\Windows\WindowManager as WindowManagerImplementation;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NativeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('nativephp')
            ->hasCommands([
                DebugCommand::class,
                FreshCommand::class,
                MigrateCommand::class,
                SeedDatabaseCommand::class,
                WipeDatabaseCommand::class,
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

        $this->app->bind(WindowManagerContract::class, function (Foundation $app) {
            return $app->make(WindowManagerImplementation::class);
        });

        $this->app->bind(ChildProcessContract::class, function (Foundation $app) {
            return $app->make(ChildProcessImplementation::class);
        });

        $this->app->bind(GlobalShortcutContract::class, function (Foundation $app) {
            return $app->make(GlobalShortcutImplementation::class);
        });

        $this->app->bind(PowerMonitorContract::class, function (Foundation $app) {
            return $app->make(PowerMonitorImplementation::class);
        });

        $this->app->bind(QueueWorkerContract::class, function (Foundation $app) {
            return $app->make(QueueWorker::class);
        });

        if (config('nativephp-internal.running')) {
            $this->app->singleton(
                \Illuminate\Contracts\Debug\ExceptionHandler::class,
                Handler::class
            );

            // Automatically prevent browser access
            $this->app->make(Kernel::class)->pushMiddleware(
                PreventRegularBrowserAccess::class,
            );

            Application::starting(function ($app) {
                $app->resolveCommands([
                    LoadStartupConfigurationCommand::class,
                    LoadPHPConfigurationCommand::class,
                    MigrateCommand::class,
                ]);
            });

            $this->configureApp();
        }
    }

    public function bootingPackage()
    {
        if (config('nativephp-internal.running')) {
            $this->rewriteDatabase();
        }
    }

    protected function configureApp()
    {
        if (config('app.debug')) {
            app(LogWatcher::class)->register();
        }

        app(EventWatcher::class)->register();

        $this->rewriteStoragePath();

        $this->configureDisks();

        config(['session.driver' => 'file']);
        config(['queue.default' => 'database']);

        // XXX: This logic may need to change when we ditch the internal web server
        if (! $this->app->runningInConsole()) {
            $this->fireUpQueueWorkers();
        }
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

        // Automatically create the database in development mode
        if (config('app.debug')) {
            $databasePath = database_path('nativephp.sqlite');

            if (! file_exists($databasePath)) {
                touch($databasePath);

                Artisan::call('native:migrate');
            }
        }

        config([
            'database.connections.nativephp' => [
                'driver' => 'sqlite',
                'url' => env('DATABASE_URL'),
                'database' => $databasePath,
                'prefix' => '',
                'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            ],
        ]);

        config(['database.default' => 'nativephp']);
        config(['queue.failed.database' => 'nativephp']);
        config(['queue.batching.database' => 'nativephp']);
        config(['queue.connections.database.connection' => 'nativephp']);

        if (file_exists($databasePath)) {
            DB::statement('PRAGMA journal_mode=WAL;');
            DB::statement('PRAGMA busy_timeout=5000;');
        }
    }

    public function removeDatabase()
    {
        $databasePath = config('nativephp-internal.database_path');

        if (config('app.debug')) {
            $databasePath = database_path('nativephp.sqlite');
        }

        @unlink($databasePath);
        @unlink($databasePath.'-shm');
        @unlink($databasePath.'-wal');
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

            config([
                'filesystems.disks.'.$disk => [
                    'driver' => 'local',
                    'root' => env($env, ''),
                    'throw' => false,
                    'links' => 'skip',
                ],
            ]);
        }
    }

    protected function fireUpQueueWorkers(): void
    {
        $queueConfigs = QueueConfig::fromConfigArray(config('nativephp.queue_workers'));

        foreach ($queueConfigs as $queueConfig) {
            $this->app->make(QueueWorkerContract::class)->up($queueConfig);
        }
    }
}
