<?php

namespace Native\Electron\Commands\Bifrost;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Native\Electron\Traits\HandlesBifrost;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[AsCommand(
    name: 'bifrost:login',
    description: 'Login to Bifrost and store API token.',
)]
class LoginCommand extends Command
{
    use HandlesBifrost;

    protected $signature = 'bifrost:login';

    public function handle(): int
    {
        intro('Welcome to Bifrost! Please enter your credentials.');

        $email = text(
            label: 'Email',
            required: true,
            validate: fn (string $value) => match (true) {
                ! filter_var($value, FILTER_VALIDATE_EMAIL) => 'Please enter a valid email address.',
                default => null
            }
        );

        $password = password(
            label: 'Password',
            required: true
        );

        $this->line('');
        $this->info('Logging in...');

        $response = Http::acceptJson()
            ->post($this->baseUrl().'api/v1/auth/login', [
                'email' => $email,
                'password' => $password,
            ]);

        if ($response->failed()) {
            $this->line('');
            $this->error('Login failed: '.$response->json('message', 'Invalid credentials'));

            return static::FAILURE;
        }

        $data = $response->json('data');
        $token = $data['token'];

        // Store token in .env file
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        if (str_contains($envContent, 'BIFROST_TOKEN=')) {
            $envContent = preg_replace('/BIFROST_TOKEN=.*/', "BIFROST_TOKEN={$token}", $envContent);
        } else {
            $envContent .= "\nBIFROST_TOKEN={$token}";
        }

        file_put_contents($envPath, $envContent);

        // Fetch user info
        $userResponse = Http::acceptJson()
            ->withToken($token)
            ->get($this->baseUrl().'api/v1/auth/user');

        if ($userResponse->successful()) {
            $user = $userResponse->json('data');
            $this->line('');
            $this->info('Successfully logged in!');
            $this->line('User: '.$user['name'].' ('.$user['email'].')');
        } else {
            $this->line('');
            $this->info('Successfully logged in!');
        }

        $this->line('');
        $this->line('Next step: Run "php artisan bifrost:init" to select a project.');

        return static::SUCCESS;
    }
}
