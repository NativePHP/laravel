<?php

use Native\Laravel\DTOs\QueueConfig;
use Native\Laravel\Facades\ChildProcess;
use Native\Laravel\Facades\QueueWorker;

it('hits the child process with relevant queue config to spin up a new queue worker', function () {
    ChildProcess::fake();
    $config = new QueueConfig('some_worker', ['default'], 128, 61);

    QueueWorker::up($config);

    ChildProcess::assertPhp(function (array $cmd, string $alias, $env, $persistent) {
        expect($cmd)->toBe([
            '-d',
            'memory_limit=128M',
            'artisan',
            'queue:work',
            "--name={$alias}",
            '--queue=default',
            '--memory=128',
            '--timeout=61',
        ]);

        expect($alias)->toBe('some_worker');
        expect($env)->toBeNull();
        expect($persistent)->toBeTrue();

        return true;
    });
});

it('hits the child process with relevant alias spin down a queue worker', function () {
    ChildProcess::fake();

    QueueWorker::down('some_worker');

    ChildProcess::assertStop('some_worker');
});
