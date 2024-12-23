<?php

use Native\Laravel\Contracts\QueueWorker as QueueWorkerContract;
use Native\Laravel\DTOs\QueueConfig;
use Native\Laravel\Facades\QueueWorker;
use Native\Laravel\Fakes\QueueWorkerFake;
use PHPUnit\Framework\AssertionFailedError;

use function Pest\Laravel\swap;

it('swaps implementations using facade', function () {
    QueueWorker::fake();

    expect(app(QueueWorkerContract::class))->toBeInstanceOf(QueueWorkerFake::class);
});

it('asserts up using callable', function () {
    swap(QueueWorkerContract::class, $fake = app(QueueWorkerFake::class));

    $fake->up(new QueueConfig('testA', ['default'], 123, 123));
    $fake->up(new QueueConfig('testB', ['default'], 123, 123));

    $fake->assertUp(fn (QueueConfig $up) => $up->alias === 'testA');
    $fake->assertUp(fn (QueueConfig $up) => $up->alias === 'testB');

    try {
        $fake->assertUp(fn (QueueConfig $up) => $up->alias === 'testC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts down using string', function () {
    swap(QueueWorkerContract::class, $fake = app(QueueWorkerFake::class));

    $fake->down('testA');
    $fake->down('testB');

    $fake->assertDown('testA');
    $fake->assertDown('testB');

    try {
        $fake->assertDown('testC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts down using callable', function () {
    swap(QueueWorkerContract::class, $fake = app(QueueWorkerFake::class));

    $fake->down('testA');
    $fake->down('testB');

    $fake->assertDown(fn (string $alias) => $alias === 'testA');
    $fake->assertDown(fn (string $alias) => $alias === 'testB');

    try {
        $fake->assertDown(fn (string $alias) => $alias === 'testC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});
