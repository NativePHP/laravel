<?php

namespace Native\Electron\Commands\Bifrost;

use Carbon\CarbonInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Native\Electron\Traits\HandlesBifrost;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\intro;

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
        try {
            $this->validateAuthAndGetUser();
        } catch (Exception $e) {
            $this->error($e->getMessage());
            $this->line('Run: php artisan bifrost:login');

            return static::FAILURE;
        }

        if (! $this->checkForBifrostProject()) {
            return static::FAILURE;
        }

        intro('Fetching latest desktop bundle...');

        try {
            $projectId = config('nativephp-internal.bifrost.project');
            $response = $this->makeApiRequest('GET', "api/v1/projects/{$projectId}/builds/latest-desktop-bundle");

            if ($response->failed()) {
                $this->handleApiError($response);

                return static::FAILURE;
            }

            $buildData = $response->json();

            if (! isset($buildData['data']['download_url'])) {
                $this->error('Bundle download URL not found in response.');

                return static::FAILURE;
            }

            $this->displayBundleInfo($buildData['data']);

            $bundlePath = $this->prepareBundlePath();

            if (! $this->downloadBundle($buildData['data']['download_url'], $bundlePath)) {
                return static::FAILURE;
            }

            // Download GPG signature if available
            $signaturePath = null;
            if (isset($buildData['data']['signature_url'])) {
                $signaturePath = $bundlePath.'.asc';
                if (! $this->downloadSignature($buildData['data']['signature_url'], $signaturePath)) {
                    $this->warn('Failed to download GPG signature file.');
                }
            }

            $this->displaySuccessInfo($bundlePath, $signaturePath);

            return static::SUCCESS;
        } catch (Exception $e) {
            $this->error('Failed to download bundle: '.$e->getMessage());

            return static::FAILURE;
        }
    }

    private function displayBundleInfo(array $buildData): void
    {
        $this->line('');
        $this->info('Bundle Details:');
        $this->line('Version: '.($buildData['version'] ?? 'Unknown'));
        $this->line('Git Commit: '.substr($buildData['git_commit'] ?? '', 0, 8));
        $this->line('Git Branch: '.($buildData['git_branch'] ?? 'Unknown'));
        $this->line('Created: '.($buildData['created_at'] ?? 'Unknown'));
    }

    private function prepareBundlePath(): string
    {
        $buildDir = base_path('build');
        if (! is_dir($buildDir)) {
            mkdir($buildDir, 0755, true);
        }

        return base_path('build/__nativephp_app_bundle');
    }

    private function downloadBundle(string $downloadUrl, string $bundlePath): bool
    {
        $this->line('');
        $this->info('Downloading bundle...');

        $progressBar = $this->output->createProgressBar();
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

        try {
            $downloadResponse = Http::withOptions([
                'sink' => $bundlePath,
                'progress' => function ($downloadTotal, $downloadedBytes) use ($progressBar) {
                    if ($downloadTotal > 0) {
                        $progressBar->setMaxSteps($downloadTotal);
                        $progressBar->setProgress($downloadedBytes);
                        $progressBar->setMessage(sprintf('%.1f MB', $downloadedBytes / 1024 / 1024));
                    }
                },
            ])->get($downloadUrl);

            $progressBar->finish();
            $this->line('');

            if ($downloadResponse->failed()) {
                $this->error('Failed to download bundle.');
                $this->cleanupFailedDownload($bundlePath);

                return false;
            }

            return true;
        } catch (Exception $e) {
            $progressBar->finish();
            $this->line('');
            $this->error('Download failed: '.$e->getMessage());
            $this->cleanupFailedDownload($bundlePath);

            return false;
        }
    }

    private function cleanupFailedDownload(string $bundlePath): void
    {
        if (file_exists($bundlePath)) {
            unlink($bundlePath);
            $this->line('Cleaned up partial download.');
        }
    }

    private function downloadSignature(string $signatureUrl, string $signaturePath): bool
    {
        $this->line('');
        $this->info('Downloading GPG signature...');

        try {
            $downloadResponse = Http::get($signatureUrl);

            if ($downloadResponse->failed()) {
                return false;
            }

            file_put_contents($signaturePath, $downloadResponse->body());

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function displaySuccessInfo(string $bundlePath, ?string $signaturePath = null): void
    {
        $this->line('');
        $this->info('Bundle downloaded successfully!');
        $this->line('Location: '.$bundlePath);

        if (file_exists($bundlePath)) {
            $sizeInMB = number_format(filesize($bundlePath) / 1024 / 1024, 2);
            $this->line("Size: {$sizeInMB} MB");
        }

        if ($signaturePath && file_exists($signaturePath)) {
            $this->line('GPG Signature: '.$signaturePath);
            $this->line('');
            $this->info('To verify the bundle integrity:');
            $this->line('gpg --verify '.basename($signaturePath).' '.basename($bundlePath));
        }
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
                $teamSlug = $this->getCurrentTeamSlug();
                $projectId = config('nativephp-internal.bifrost.project');
                $baseUrl = rtrim($this->baseUrl(), '/');

                if ($teamSlug && $projectId) {
                    $this->info("Create a build at: {$baseUrl}/{$teamSlug}/desktop/projects/{$projectId}");
                } else {
                    $this->info("Visit the dashboard: {$baseUrl}/dashboard");
                }
                break;

            case 503:
                $retryAfter = intval($response->header('Retry-After'));
                $diff = now()->addSeconds($retryAfter);
                $diffMessage = $retryAfter <= 60 ? 'a minute' : $diff->diffForHumans(syntax: CarbonInterface::DIFF_ABSOLUTE);
                $this->line('');
                $this->warn('Build is still in progress.');
                $this->line("Please try again in {$diffMessage}.");
                break;

            case 500:
                $this->line('');
                $this->error('Latest build has failed or was cancelled.');
                if (isset($data['build_id'])) {
                    $this->line('Build ID: '.$data['build_id']);
                }
                if (isset($data['status'])) {
                    $this->line('Status: '.$data['status']);
                }
                break;

            default:
                $this->line('');
                $this->error('Failed to fetch bundle: '.($data['message'] ?? 'Unknown error'));
        }
    }
}
