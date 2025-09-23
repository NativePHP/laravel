<?php

namespace Native\Electron\Commands\Bifrost;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Native\Electron\Traits\HandlesBifrost;
use Native\Electron\Traits\ManagesEnvFile;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\intro;

#[AsCommand(
    name: 'bifrost:logout',
    description: 'Logout from Bifrost and remove API token.',
)]
class LogoutCommand extends Command
{
    use HandlesBifrost;
    use ManagesEnvFile;

    protected $signature = 'bifrost:logout';

    public function handle(): int
    {
        if (! $this->checkForBifrostToken()) {
            $this->warn('You are not logged in.');

            return static::SUCCESS;
        }

        intro('Logging out from Bifrost...');

        // Attempt to logout on the server
        Http::acceptJson()
            ->withToken(config('nativephp-internal.bifrost.token'))
            ->post($this->baseUrl().'api/v1/auth/logout');

        // Remove token and project from .env file regardless of server response
        $this->removeFromEnvFile(['BIFROST_TOKEN', 'BIFROST_PROJECT']);

        $this->info('Successfully logged out!');
        $this->line('Your API token and project selection have been removed.');

        return static::SUCCESS;
    }
}
