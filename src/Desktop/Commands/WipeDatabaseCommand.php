<?php

namespace Native\Desktop\Commands;

use Illuminate\Database\Console\WipeCommand as BaseWipeCommand;
use Native\Desktop\NativeServiceProvider;

class WipeDatabaseCommand extends BaseWipeCommand
{
    protected $name = 'native:db:wipe';

    protected $description = 'Wipe the database in the NativePHP development environment';

    public function handle()
    {
        (new NativeServiceProvider($this->laravel))->rewriteDatabase();

        return parent::handle();
    }
}
