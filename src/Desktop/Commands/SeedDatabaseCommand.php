<?php

namespace Native\Desktop\Commands;

use Illuminate\Database\Console\Seeds\SeedCommand as BaseSeedCommand;
use Native\Desktop\NativeServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(
    name: 'native:seed',
    description: 'Seed the database in the NativePHP development environment',
)]
class SeedDatabaseCommand extends BaseSeedCommand
{
    protected $signature = 'native:seed';

    protected function configure(): void
    {
        parent::configure();

        $this->addArgument('class', mode: InputArgument::OPTIONAL, description: 'The class name of the root seeder');
        $this->addOption('class', mode: InputOption::VALUE_OPTIONAL, description: 'The class name of the root seeder', default: 'Database\\Seeders\\DatabaseSeeder');
    }

    public function handle()
    {
        // Add the database option here so it won't show up in `--help`
        $this->addOption('database', mode: InputOption::VALUE_REQUIRED, default: 'nativephp');

        (new NativeServiceProvider($this->laravel))->rewriteDatabase();

        return parent::handle();
    }
}
