<?php

namespace Native\Laravel\Commands;

use Illuminate\Console\Command;

class LoadStartupConfigurationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'native:config';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo json_encode(config('nativephp'));
        return 0;
    }
}
