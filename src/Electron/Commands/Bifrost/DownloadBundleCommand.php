<?php

namespace Native\Electron\Commands\Bifrost;

use Carbon\CarbonInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Native\Electron\Traits\HandlesBifrost;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\progress;

#[AsCommand(
    name: 'bifrost:download-bundle',
    description: 'Download the latest desktop bundle from Bifrost.',
)]
class DownloadBundleCommand extends Command
{
    use HandlesBifrost;

    protected $signature = 'bifrost:download-bundle';

    public function handle(): int
    {
        if (! $this->checkForBifrostToken()) {
            return static::FAILURE;
        }

        if (! $this->checkForBifrostProject()) {
            return static::FAILURE;
        }

        if (! $this->checkAuthenticated()) {
            $this->error('Invalid API token. Please login again.');
            $this->line('Run: php artisan bifrost:login');

            return static::FAILURE;
        }

        intro('Fetching latest desktop bundle...');

        $projectId = config('nativephp-internal.bifrost.project');
        $response = Http::acceptJson()
            ->withToken(config('nativephp-internal.bifrost.token'))
            ->get($this->baseUrl()."api/v1/projects/{$projectId}/builds/latest-desktop-bundle");

        if ($response->failed()) {
            $this->handleApiError($response);

            return static::FAILURE;
        }

        $buildData = $response->json();
        $downloadUrl = $buildData['download_url'];

        $this->line('');
        $this->info('Bundle Details:');
        $this->line('Version: '.$buildData['version']);
        $this->line('Git Commit: '.substr($buildData['git_commit'], 0, 8));
        $this->line('Git Branch: '.$buildData['git_branch']);
        $this->line('Created: '.$buildData['created_at']);

        // Create build directory if it doesn't exist
        $buildDir = base_path('build');
        if (! is_dir($buildDir)) {
            mkdir($buildDir, 0755, true);
        }

        $bundlePath = base_path('build/__nativephp_app_bundle');

        // Download the bundle with progress bar
        $this->line('');
        $this->info('Downloading bundle...');

        $downloadResponse = Http::withOptions([
            'sink' => $bundlePath,
            'progress' => function ($downloadTotal, $downloadedBytes) {
                if ($downloadTotal > 0) {
                    $progress = ($downloadedBytes / $downloadTotal) * 100;
                    $this->output->write("\r".sprintf('Progress: %.1f%%', $progress));
                }
            },
        ])->get($downloadUrl);

        if ($downloadResponse->failed()) {
            $this->line('');
            $this->error('Failed to download bundle.');

            if (file_exists($bundlePath)) {
                unlink($bundlePath);
            }

            return static::FAILURE;
        }

        $this->line('');
        $this->line('');
        $this->info('Bundle downloaded successfully!');
        $this->line('Location: '.$bundlePath);
        $this->line('Size: '.number_format(filesize($bundlePath) / 1024 / 1024, 2).' MB');

        return static::SUCCESS;
    }

    private function handleApiError($response): void
    {
        $status = $response->status();
        $data = $response->json();

        switch ($status) {
            case 404:
                $this->line('');
                $this->error('No desktop builds found for this project.');
                $this->line('');
                $this->info('Create a build at: '.$this->baseUrl().'{team}/desktop/projects/{project}');
                break;

            case 503:
                $retryAfter = intval($response->header('Retry-After'));
                $diff = now()->addSeconds($retryAfter);
                $diffMessage = $retryAfter <= 60 ? 'a minute' : $diff->diffForHumans(syntax: CarbonInterface::DIFF_ABSOLUTE);
                $this->line('');
                $this->warn('Build is still in progress.');
                $this->line('Please try again in '.$diffMessage.'.');
                break;

            case 500:
                $this->line('');
                $this->error('Latest build has failed or was cancelled.');
                if (isset($data['build_id'])) {
                    $this->line('Build ID: '.$data['build_id']);
                    $this->line('Status: '.$data['status']);
                }
                break;

            default:
                $this->line('');
                $this->error('Failed to fetch bundle: '.($data['message'] ?? 'Unknown error'));
        }
    }
}