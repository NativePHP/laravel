<?php

namespace Native\Laravel\Commands;

use Illuminate\Database\Console\Seeds\SeedCommand as BaseSeedCommand;
use Native\Laravel\NativeServiceProvider;

class SeedDatabaseCommand extends BaseSeedCommand
{
    protected $name = 'native:db:seed';

    protected $description = 'Run the database seeders in the NativePHP development environment';

    public function handle()
    {
        (new NativeServiceProvider($this->laravel))->rewriteDatabase();

        parent::handle();
    }
}
