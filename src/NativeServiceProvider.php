<?php

namespace Native\Laravel;

use Illuminate\Foundation\Console\ServeCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NativeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('native-php')
            ->hasConfigFile()
            ->hasRoute('api')
            ->publishesServiceProvider('NativeAppServiceProvider');
    }

    public function bootingPackage()
    {
        ServeCommand::$passthroughVariables[] = 'NATIVE_PHP_SECRET';
    }
}
