<?php

namespace Native\Electron\Commands\Bifrost;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\intro;

#[AsCommand(
    name: 'bifrost:clear-bundle',
    description: 'Remove the downloaded bundle from the build directory.',
)]
class ClearBundleCommand extends Command
{
    protected $signature = 'bifrost:clear-bundle';

    public function handle(): int
    {
        intro('Clearing downloaded bundle...');

        $bundlePath = base_path('build/__nativephp_app_bundle');
        $signaturePath = $bundlePath.'.asc';

        $bundleExists = file_exists($bundlePath);
        $signatureExists = file_exists($signaturePath);

        if (! $bundleExists && ! $signatureExists) {
            $this->warn('No bundle or signature files found to clear.');

            return static::SUCCESS;
        }

        $cleared = [];
        $failed = [];

        if ($bundleExists) {
            if (unlink($bundlePath)) {
                $cleared[] = 'bundle';
            } else {
                $failed[] = 'bundle';
            }
        }

        if ($signatureExists) {
            if (unlink($signaturePath)) {
                $cleared[] = 'GPG signature';
            } else {
                $failed[] = 'GPG signature';
            }
        }

        if (! empty($cleared)) {
            $clearedText = implode(' and ', $cleared);
            $this->info("Cleared {$clearedText} successfully!");
            $this->line('Note: Building in this state would be unsecure without a valid bundle.');
        }

        if (! empty($failed)) {
            $failedText = implode(' and ', $failed);
            $this->error("Failed to remove {$failedText}.");

            return static::FAILURE;
        }

        return static::SUCCESS;
    }
}
