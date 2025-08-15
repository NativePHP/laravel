<?php

namespace Native\Laravel\Commands;

use Illuminate\Database\Console\Seeds\SeedCommand as BaseSeedCommand;
use Native\Laravel\NativeServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'native:seed',
    description: 'Seed the database in the NativePHP development environment',
)]
class SeedDatabaseCommand extends BaseSeedCommand
{
    protected $signature = 'native:seed';

    public function handle()
    {
        $this->addOption('database', default: null);
        $this->addArgument('class', default: 'DatabaseSeeder');

        (new NativeServiceProvider($this->laravel))->rewriteDatabase();

        return parent::handle();
    }
}
