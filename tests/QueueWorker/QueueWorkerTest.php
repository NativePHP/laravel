<?php

use Native\Desktop\DataObjects\QueueConfig;
use Native\Desktop\Facades\ChildProcess;
use Native\Desktop\Facades\QueueWorker;

it('hits the child process with relevant queue config to spin up a new queue worker', function () {
    ChildProcess::fake();

    $workerName = 'some_worker';

    $config = new QueueConfig($workerName, ['default'], 128, 61, 5);

    QueueWorker::up($config);

    ChildProcess::assertArtisan(function (array $cmd, string $alias, $env, $persistent, $iniSettings) use ($workerName) {
        expect($cmd)->toBe([
            'queue:work',
            "--name={$workerName}",
            '--queue=default',
            '--memory=128',
            '--timeout=61',
            '--sleep=5',
        ]);

        expect($iniSettings)->toBe([
            'memory_limit' => '128M',
        ]);

        expect($alias)->toBe('queue_some_worker');
        expect($env)->toBeNull();
        expect($persistent)->toBeTrue();

        return true;
    });
});

it('hits the child process with relevant alias spin down a queue worker', function () {
    ChildProcess::fake();

    QueueWorker::down('some_worker');

    ChildProcess::assertStop('queue_some_worker');
});
