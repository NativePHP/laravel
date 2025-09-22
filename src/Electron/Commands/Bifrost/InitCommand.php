<?php

namespace Native\Electron\Commands\Bifrost;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Native\Electron\Traits\HandlesBifrost;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\select;

#[AsCommand(
    name: 'bifrost:init',
    description: 'Select a desktop project for Bifrost operations.',
)]
class InitCommand extends Command
{
    use HandlesBifrost;

    protected $signature = 'bifrost:init';

    public function handle(): int
    {
        if (! $this->checkForBifrostToken()) {
            return static::FAILURE;
        }

        if (! $this->checkAuthenticated()) {
            $this->error('Invalid API token. Please login again.');
            $this->line('Run: php artisan bifrost:login');

            return static::FAILURE;
        }

        intro('Fetching your desktop projects...');

        $response = Http::acceptJson()
            ->withToken(config('nativephp-internal.bifrost.token'))
            ->get($this->baseUrl().'api/v1/projects');

        if ($response->failed()) {
            $this->handleApiError($response);

            return static::FAILURE;
        }

        $projects = collect($response->json('data'))
            ->filter(fn ($project) => $project['type'] === 'desktop')
            ->values()
            ->toArray();

        if (empty($projects)) {
            $this->line('');
            $this->warn('No desktop projects found.');
            $this->line('');
            $this->info('Create a desktop project at: '.$this->baseUrl().'{team}/onboarding/project/desktop');

            return static::FAILURE;
        }

        $choices = [];
        foreach ($projects as $project) {
            $choices[$project['id']] = $project['name'].' - '.$project['repo'];
        }

        $selectedProjectId = select(
            label: 'Select a desktop project',
            options: $choices,
            required: true
        );

        $selectedProject = collect($projects)->firstWhere('id', $selectedProjectId);

        // Store project in .env file
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        if (str_contains($envContent, 'BIFROST_PROJECT=')) {
            $envContent = preg_replace('/BIFROST_PROJECT=.*/', "BIFROST_PROJECT={$selectedProjectId}", $envContent);
        } else {
            $envContent .= "\nBIFROST_PROJECT={$selectedProjectId}";
        }

        file_put_contents($envPath, $envContent);

        $this->line('');
        $this->info('Project selected successfully!');
        $this->line('Project: '.$selectedProject['name']);
        $this->line('Repository: '.$selectedProject['repo']);
        $this->line('');
        $this->line('You can now run "php artisan bifrost:download-bundle" to download the latest bundle.');

        return static::SUCCESS;
    }

    private function handleApiError($response): void
    {
        $status = $response->status();
        $baseUrl = rtrim($this->baseUrl(), '/');

        switch ($status) {
            case 403:
                $this->line('');
                $this->error('No teams found. Please create a team first.');
                $this->line('');
                $this->info('Create a team at: '.$baseUrl.'/onboarding/team');
                break;

            case 422:
                $this->line('');
                $this->error('Team setup incomplete or subscription required.');
                $this->line('');
                $this->info('Complete setup at: '.$baseUrl.'/dashboard');
                break;

            default:
                $this->line('');
                $this->error('Failed to fetch projects: '.$response->json('message', 'Unknown error'));
                $this->line('');
                $this->info('Visit the dashboard: '.$baseUrl.'/dashboard');
        }
    }
}