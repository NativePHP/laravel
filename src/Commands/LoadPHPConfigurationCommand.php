<?php

namespace Native\Laravel\Commands;

use Illuminate\Console\Command;
use Native\Laravel\Contracts\ProvidesPhpIni;

class LoadPHPConfigurationCommand extends Command
{
    protected $signature = 'native:php-ini';

    public function handle()
    {
        /** @var ProvidesPhpIni $provider */
        $provider = app(config('nativephp.provider'));
        $phpIni = [];
        if (method_exists($provider, 'phpIni')) {
            $phpIni = $provider->phpIni();
        }
        echo json_encode($phpIni);
    }
}
