<?php

use Illuminate\Support\Facades\Pipeline;
use Native\Laravel\Events\App\ProjectFileChanged;
use Native\Laravel\Facades\QueueWorker;
use Native\Laravel\Pipelines\ProjectFileChanged\RestartQueueWorkers;

it('restarts configured queue workers', function () {
    QueueWorker::fake();

    config(['nativephp.queue_workers' => [
        'something' => [],
        'another' => [],
    ]]);

    Pipeline::send(new ProjectFileChanged('some/file.php'))
        ->through([RestartQueueWorkers::class])
        ->thenReturn();

    QueueWorker::assertDown('something');
    QueueWorker::assertDown('another');
});
