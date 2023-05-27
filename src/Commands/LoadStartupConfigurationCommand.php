<?php

namespace Native\Laravel\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class LoadStartupConfigurationCommand extends Command
{
    protected $signature = 'native:config';

    public function handle()
    {
        echo json_encode(config('nativephp'));
    }
}
