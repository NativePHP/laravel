<?php

namespace Native\Electron\Commands\Bifrost;

use Exception;
use Illuminate\Console\Command;
use Native\Electron\Traits\HandlesBifrost;
use Native\Electron\Traits\ManagesEnvFile;
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
    use ManagesEnvFile;

    protected $signature = 'bifrost:init';

    public function handle(): int
    {
        try {
            $user = $this->validateAuthAndGetUser();
        } catch (Exception $e) {
            $this->error($e->getMessage());
            $this->line('Run: php artisan bifrost:login');

            return static::FAILURE;
        }

        intro('Fetching your desktop projects...');

        try {
            $response = $this->makeApiRequest('GET', 'api/v1/projects');

            if ($response->failed()) {
                $this->handleApiError($response);

                return static::FAILURE;
            }

            $responseData = $response->json();

            if (! isset($responseData['data']) || ! is_array($responseData['data'])) {
                $this->error('Invalid API response format.');

                return static::FAILURE;
            }

            $projects = collect($responseData['data'])
                ->filter(fn ($project) => isset($project['type']) && $project['type'] === 'desktop')
                ->values()
                ->toArray();

            if (empty($projects)) {
                $this->displayNoProjectsMessage($user);

                return static::FAILURE;
            }

            $choices = collect($projects)->mapWithKeys(function ($project) {
                $name = $project['name'] ?? 'Unknown';
                $repo = $project['repo'] ?? 'No repository';

                return [$project['uuid'] => "{$name} - {$repo}"];
            })->toArray();

            $selectedProjectUuid = select(
                label: 'Select a desktop project',
                options: $choices,
                required: true
            );

            $selectedProject = collect($projects)->firstWhere('uuid', $selectedProjectUuid);

            if (! $selectedProject) {
                $this->error('Selected project not found.');

                return static::FAILURE;
            }

            // Store project UUID in .env file
            $this->updateEnvFile('BIFROST_PROJECT', $selectedProjectUuid);

            $this->displaySuccessMessage($selectedProject);

            return static::SUCCESS;
        } catch (Exception $e) {
            $this->error('Failed to fetch projects: '.$e->getMessage());

            return static::FAILURE;
        }
    }

    private function displayNoProjectsMessage(array $user): void
    {
        $this->line('');
        $this->warn('No desktop projects found.');
        $this->line('');

        $teamSlug = $user['current_team']['slug'] ?? null;
        $baseUrl = rtrim($this->baseUrl(), '/');

        if ($teamSlug) {
            $this->info("Create a desktop project at: {$baseUrl}/{$teamSlug}/onboarding/project/desktop");
        } else {
            $this->info("Create a desktop project at: {$baseUrl}/onboarding/project/desktop");
        }
    }

    private function displaySuccessMessage(array $project): void
    {
        $this->line('');
        $this->info('Project selected successfully!');
        $this->line('Project: '.($project['name'] ?? 'Unknown'));
        $this->line('Repository: '.($project['repo'] ?? 'Unknown'));
        $this->line('');
        $this->line('You can now run "php artisan bifrost:download-bundle" to download the latest bundle.');
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
                $this->info("Create a team at: {$baseUrl}/onboarding/team");
                break;

            case 422:
                $this->line('');
                $this->error('Team setup incomplete or subscription required.');
                $this->line('');
                $this->info("Complete setup at: {$baseUrl}/dashboard");
                break;

            default:
                $this->line('');
                $this->error('Failed to fetch projects: '.$response->json('message', 'Unknown error'));
                $this->line('');
                $this->info("Visit the dashboard: {$baseUrl}/dashboard");
        }
    }
}
