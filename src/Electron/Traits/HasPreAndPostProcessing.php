<?php

namespace Native\Electron\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;

trait HasPreAndPostProcessing
{
    public function preProcess(): void
    {
        $config = collect(config('nativephp.prebuild'));

        if ($config->isEmpty()) {
            return;
        }

        intro('Running pre-process commands...');

        $this->runProcess($config);

        outro('Pre-process commands completed.');
    }

    public function postProcess(): void
    {
        $config = collect(config('nativephp.postbuild'));

        if ($config->isEmpty()) {
            return;
        }

        intro('Running post-process commands...');

        $this->runProcess($config);

        outro('Post-process commands completed.');
    }

    private function runProcess(Collection $configCommands): void
    {
        $configCommands->each(function ($command) {
            note("Running command: {$command}");

            if (is_array($command)) {
                $command = implode(' && ', $command);
            }

            $result = Process::path(base_path())
                ->timeout(300)
                ->tty(\Symfony\Component\Process\Process::isTtySupported())
                ->run($command, function (string $type, string $output) {
                    echo $output;
                });

            if (! $result->successful()) {
                error("Command failed: {$command}");

                return;
            }

            note("Command successful: {$command}");
        });
    }
}
