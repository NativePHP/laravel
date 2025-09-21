<?php

namespace Native\Laravel\Commands;

use Illuminate\Database\Console\Migrations\FreshCommand as BaseFreshCommand;
use Native\Laravel\NativeServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'native:migrate:fresh',
    description: 'Drop all tables and re-run all migrations in the NativePHP development environment',
)]
class FreshCommand extends BaseFreshCommand
{
    protected $name = 'native:migrate:fresh';

    protected $description = 'Drop all tables and re-run all migrations in the NativePHP development environment';

    public function handle()
    {
        $nativeServiceProvider = new NativeServiceProvider($this->laravel);

        $nativeServiceProvider->removeDatabase();

        $nativeServiceProvider->rewriteDatabase();

        return parent::handle();
    }
}
