<?php

namespace Native\Electron\Commands\Bifrost;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Native\Electron\Traits\HandlesBifrost;
use Native\Electron\Traits\ManagesEnvFile;
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
    use ManagesEnvFile;

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

        try {
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

            $responseData = $response->json();

            if (! isset($responseData['data']['token'])) {
                $this->line('');
                $this->error('Login response missing token. Please try again.');

                return static::FAILURE;
            }

            $token = $responseData['data']['token'];

            // Store token in .env file
            $this->updateEnvFile('BIFROST_TOKEN', $token);

            // Fetch and display user info
            $this->displayUserInfo($token);

            $this->line('');
            $this->line('Next step: Run "php artisan bifrost:init" to select a project.');

            return static::SUCCESS;
        } catch (Exception $e) {
            $this->line('');
            $this->error('Network error: '.$e->getMessage());

            return static::FAILURE;
        }
    }

    private function displayUserInfo(string $token): void
    {
        try {
            $userResponse = Http::acceptJson()
                ->withToken($token)
                ->get($this->baseUrl().'api/v1/auth/user');

            if ($userResponse->successful()) {
                $userData = $userResponse->json();

                if (isset($userData['data'])) {
                    $user = $userData['data'];
                    $this->line('');
                    $this->info('Successfully logged in!');
                    $this->line('User: '.($user['name'] ?? 'Unknown').' ('.($user['email'] ?? 'Unknown').')');

                    if (isset($user['current_team']['name'])) {
                        $this->line('Team: '.$user['current_team']['name']);
                    }

                    return;
                }
            }
        } catch (Exception $e) {
            // Silently fail user info display - login was successful
        }

        $this->line('');
        $this->info('Successfully logged in!');
    }
}
