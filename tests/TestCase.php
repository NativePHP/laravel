<?php

namespace Native\Electron\Tests;

use Illuminate\Support\Facades\Artisan;
use Native\Electron\ElectronServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('native:install', ['--force' => true]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            ElectronServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }
}
