<?php

namespace Native\Electron\Tests;

use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

#[WithConfig('database.default', 'testing')]
class TestCase extends Orchestra
{
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('native:install', ['--force' => true, '--no-interaction' => true]);
    }
}
