<?php

namespace Native\Laravel\Pipelines\ProjectFileChanged;

use Closure;
use Native\Laravel\Contracts\QueueWorker;
use Native\Laravel\DTOs\QueueConfig;
use Native\Laravel\Events\App\ProjectFileChanged;

class RestartQueueWorkers
{
    public function __construct(
        private readonly QueueWorker $queueWorker,
    ) {}

    public function __invoke(ProjectFileChanged $event, Closure $next): ProjectFileChanged
    {
        $queueConfigs = QueueConfig::fromConfigArray(config('nativephp.queue_workers'));

        foreach ($queueConfigs as $queueConfig) {
            $this->queueWorker->down($queueConfig->alias);
        }

        return $next($event);
    }
}
