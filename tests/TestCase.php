<?php

namespace Native\Desktop\Tests;

use Native\Desktop\NativeServiceProvider;
use Native\Electron\ElectronServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            NativeServiceProvider::class,
            ElectronServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
