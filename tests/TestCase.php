<?php

namespace Native\Electron\Tests;

use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('native:install', ['--force' => true]);
    }

    public function defineEnvironment($app)
    {
        tap($app->make('config'), function ($config) {
            $config->set('database.default', 'testing');
        });
    }
}
