<?php

namespace Native\Electron\Commands\Bifrost;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Native\Electron\Traits\HandlesBifrost;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\intro;

#[AsCommand(
    name: 'bifrost:logout',
    description: 'Logout from Bifrost and remove API token.',
)]
class LogoutCommand extends Command
{
    use HandlesBifrost;

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

        // Remove token from .env file regardless of server response
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        // Remove BIFROST_TOKEN line
        $envContent = preg_replace('/^BIFROST_TOKEN=.*$/m', '', $envContent);
        // Also remove BIFROST_PROJECT when logging out
        $envContent = preg_replace('/^BIFROST_PROJECT=.*$/m', '', $envContent);

        // Clean up extra newlines
        $envContent = preg_replace('/\n\n+/', "\n\n", $envContent);
        $envContent = trim($envContent)."\n";

        file_put_contents($envPath, $envContent);

        $this->info('Successfully logged out!');
        $this->line('Your API token and project selection have been removed.');

        return static::SUCCESS;
    }
}