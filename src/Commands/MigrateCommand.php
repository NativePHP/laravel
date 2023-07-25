<?php

namespace Native\Laravel\Commands;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Console\Migrations\MigrateCommand as BaseMigrateCommand;
use Illuminate\Database\Migrations\Migrator;
use Native\Laravel\NativeServiceProvider;

class MigrateCommand extends BaseMigrateCommand
{
    protected $description = 'Run the database migrations in the NativePHP development environment';

    public function __construct(Migrator $migrator, Dispatcher $dispatcher)
    {
        $this->signature = 'native:'.$this->signature;

        parent::__construct($migrator, $dispatcher);
    }

    public function handle()
    {
        (new NativeServiceProvider($this->laravel))->rewriteDatabase();

        parent::handle();
    }
}
