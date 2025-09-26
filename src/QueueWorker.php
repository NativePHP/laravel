<?php

namespace Native\Desktop;

use Native\Desktop\Contracts\ChildProcess as ChildProcessContract;
use Native\Desktop\Contracts\QueueWorker as QueueWorkerContract;
use Native\Desktop\DataObjects\QueueConfig;

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

        $command = app()->isLocal()
            ? 'queue:listen'
            : 'queue:work';

        $this->childProcess->artisan(
            [
                $command,
                "--name={$config->alias}",
                '--queue='.implode(',', $config->queuesToConsume),
                "--memory={$config->memoryLimit}",
                "--timeout={$config->timeout}",
                "--sleep={$config->sleep}",
            ],
            'queue_'.$config->alias,
            persistent: true,
            iniSettings: [
                'memory_limit' => "{$config->memoryLimit}M",
            ]
        );
    }

    public function down(string $alias): void
    {
        $this->childProcess->stop('queue_'.$alias);
    }
}
