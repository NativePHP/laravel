<?php

namespace Native\Laravel;

use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Native\Laravel\Commands\LoadStartupConfigurationCommand;
use Native\Laravel\Commands\MinifyApplicationCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NativeServiceProvider extends PackageServiceProvider
{
    protected $passThrough = [
        'NATIVE_PHP_SECRET',
        'NATIVE_PHP_RUNNING',
        'NATIVE_PHP_STORAGE_PATH',
        'NATIVE_PHP_DATABASE_PATH',
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('native-php')
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
        foreach ($this->passThrough as $env) {
            ServeCommand::$passthroughVariables[] = $env;
        }

        if (config('native-php.running')) {
            $this->configureApp();
        }
    }

    protected function configureApp()
    {
        $oldStoragePath = $this->app->storagePath();

        $this->app->useStoragePath(config('native-php.storage_path'));

        // Patch all config values that contain the old storage path
        $config = Arr::dot(config()->all());

        foreach ($config as $key => $value) {
            if (is_string($value) && str_contains($value, $oldStoragePath)) {
                $newValue = str_replace($oldStoragePath, config('native-php.storage_path'), $value);
                config([$key => $newValue]);
            }
        }

        config(['database.connections.nativephp' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => config('native-php.database_path'),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ]]);

        config(['database.default' => 'nativephp']);
        config(['session.driver' => 'file']);

        config(['queue.default' => 'database']);
    }
}
