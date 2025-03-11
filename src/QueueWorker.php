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

    public function up(string|QueueConfig $config): void
    {
        if (is_string($config) && config()->has("nativephp.queue_workers.{$config}")) {
            $config = QueueConfig::fromConfigArray([
                $config => config("nativephp.queue_workers.{$config}"),
            ])[0];
        }

        if (! $config instanceof QueueConfig) {
            throw new \InvalidArgumentException("Invalid queue configuration alias [$config]");
        }

        $this->childProcess->artisan(
            [
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
