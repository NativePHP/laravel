<?php

namespace Native\Electron\Traits;

use function Laravel\Prompts\note;

trait Developer
{
    use ExecuteCommand;

    protected function runDeveloper(string $installer, bool $skip_queue, bool $withoutInteraction = false): void
    {
        [$installer, $command] = $this->getInstallerAndCommand(installer: $installer, type: 'dev');

        note("Running the dev script with {$installer}...");
        $this->executeCommand(command: $command, skip_queue: $skip_queue, type: 'serve', withoutInteraction: $withoutInteraction);
    }
}
