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

        if (! file_exists($bundlePath)) {
            $this->warn('No bundle found to clear.');

            return static::SUCCESS;
        }

        if (unlink($bundlePath)) {
            $this->info('Bundle cleared successfully!');
            $this->line('Note: Building in this state would be unsecure without a valid bundle.');
        } else {
            $this->error('Failed to remove bundle file.');

            return static::FAILURE;
        }

        return static::SUCCESS;
    }
}