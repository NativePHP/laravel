<?php

namespace Native\Laravel\Commands;

use Illuminate\Console\Command;
use Native\Laravel\Contracts\ProvidesPhpIni;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'native:php-ini',
    description: 'Load the PHP configuration for the NativePHP development environment',
)]
class LoadPHPConfigurationCommand extends Command
{
    protected $signature = 'native:php-ini';

    public function handle()
    {
        /** @var ProvidesPhpIni $provider */
        $provider = app(config('nativephp.provider'));
        $phpIni = [];

        /* * @phpstan-ignore-next-line */
        if (method_exists($provider, 'phpIni')) {
            $phpIni = $provider->phpIni();
        }
        echo json_encode($phpIni);
    }
}
