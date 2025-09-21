<?php

namespace Native\Laravel\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'native:config',
    description: 'Load the startup configuration for the NativePHP development environment',
)]
class LoadStartupConfigurationCommand extends Command
{
    protected $signature = 'native:config';

    public function handle()
    {
        echo json_encode(config('nativephp'));
    }
}
