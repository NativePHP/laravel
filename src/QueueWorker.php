<?php

namespace Native\Laravel;

use Native\Laravel\Contracts\ChildProcess as ChildProcessContract;
use Native\Laravel\Contracts\QueueWorker as QueueWorkerContract;
use Native\Laravel\DTOs\QueueConfig;

class QueueWorker implements QueueWorkerContract
{
    public function __construct(
        private readonly ChildProcessContract $childProcess,
    ) {}

    public function up(QueueConfig $config): void
    {
        $this->childProcess->php(
            [
                '-d',
                "memory_limit={$config->memoryLimit}M",
                'artisan',
                'queue:work',
                "--name={$config->alias}",
                '--queue='.implode(',', $config->queuesToConsume),
                "--memory={$config->memoryLimit}",
                "--timeout={$config->timeout}",
            ],
            $config->alias,
            persistent: true,
        );
    }

    public function down(string $alias): void
    {
        $this->childProcess->stop($alias);
    }
}
