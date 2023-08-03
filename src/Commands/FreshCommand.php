<?php

namespace Native\Laravel\Commands;

use Illuminate\Console\Command;
use Native\Laravel\NativeServiceProvider;

class FreshCommand extends Command
{
    protected $description = 'Run the database migrations in the NativePHP development environment';

    protected $signature = 'native:migrate fresh';

    public function handle()
    {
        unlink(config('nativephp-internal.database_path'));

        (new NativeServiceProvider($this->laravel))->rewriteDatabase();

        $this->call('native:migrate');
    }
}
