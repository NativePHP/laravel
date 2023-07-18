<?php

namespace Native\Laravel;

use Illuminate\Support\Arr;
use Native\Laravel\Commands\LoadStartupConfigurationCommand;
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
                MinifyApplicationCommand::class,
                LoadStartupConfigurationCommand::class,
            ])
            ->hasConfigFile()
            ->hasRoute('api')
            ->publishesServiceProvider('NativeAppServiceProvider');
    }

    public function packageRegistered()
    {
        $this->mergeConfigFrom($this->package->basePath('/../config/nativephp-internal.php'), 'nativephp-internal');

        if (config('nativephp-internal.running')) {
            $this->configureApp();
        }
    }

    protected function configureApp()
    {
        if (config('app.debug')) {
            app(LogWatcher::class)->register();
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

        config(['database.connections.nativephp' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => config('nativephp-internal.database_path'),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ]]);

        config(['database.default' => 'nativephp']);
        config(['session.driver' => 'file']);

        config(['queue.default' => 'database']);
    }
}
