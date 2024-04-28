<?php

namespace Native\Laravel\Commands;

use Illuminate\Database\Console\Migrations\FreshCommand as BaseFreshCommand;
use Native\Laravel\NativeServiceProvider;

class FreshCommand extends BaseFreshCommand
{
    protected $description = 'Drop all tables and re-run all migrations in the NativePHP development environment';

    protected $signature = 'native:migrate:fresh';

    public function handle()
    {
        $nativeServiceProvider = new NativeServiceProvider($this->laravel);

        $nativeServiceProvider->removeDatabase();

        $nativeServiceProvider->rewriteDatabase();

        $this->call('native:migrate');
    }
}
