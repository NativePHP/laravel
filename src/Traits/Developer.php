<?php

namespace Native\Electron\Traits;

trait Developer
{
    use ExecuteCommand;

    protected function runDeveloper(string $installer, bool $skip_queue): void
    {
        [$installer, $command] = $this->getInstallerAndCommand(installer: $installer, type: 'dev');

        $this->info("Runing the dev script with {$installer}...");
        $this->executeCommand(command: $command, type: 'serve', skip_queue: $skip_queue);
    }
}
