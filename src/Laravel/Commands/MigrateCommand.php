<?php

namespace Native\Laravel\Commands;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Console\Migrations\MigrateCommand as BaseMigrateCommand;
use Illuminate\Database\Migrations\Migrator;
use Native\Laravel\NativeServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'native:migrate',
    description: 'Run the database migrations in the NativePHP development environment',
)]
class MigrateCommand extends BaseMigrateCommand
{
    public function __construct(Migrator $migrator, Dispatcher $dispatcher)
    {
        $this->signature = 'native:'.$this->signature;

        parent::__construct($migrator, $dispatcher);
    }

    public function handle()
    {
        (new NativeServiceProvider($this->laravel))->rewriteDatabase();

        return parent::handle();
    }
}
