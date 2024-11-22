<?php

use Native\Laravel\Contracts\ChildProcess as ChildProcessContract;
use Native\Laravel\Facades\ChildProcess;
use Native\Laravel\Fakes\ChildProcessFake;
use PHPUnit\Framework\AssertionFailedError;

use function Pest\Laravel\swap;

it('swaps implementations using facade', function () {
    ChildProcess::fake();

    expect(app(ChildProcessContract::class))->toBeInstanceOf(ChildProcessFake::class);
});

it('asserts get using string', function () {
    swap(ChildProcessContract::class, $fake = app(ChildProcessFake::class));

    $fake->get('testA');
    $fake->get('testB');

    $fake->assertGet('testA');
    $fake->assertGet('testB');

    try {
        $fake->assertGet('testC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts get using callable', function () {
    swap(ChildProcessContract::class, $fake = app(ChildProcessFake::class));

    $fake->get('testA');
    $fake->get('testB');

    $fake->assertGet(fn (string $alias) => $alias === 'testA');
    $fake->assertGet(fn (string $alias) => $alias === 'testB');

    try {
        $fake->assertGet(fn (string $alias) => $alias === 'testC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts started using callable', function () {
    swap(ChildProcessContract::class, $fake = app(ChildProcessFake::class));

    $fake->start('cmdA', 'aliasA', 'cwdA', ['envA'], true);
    $fake->start('cmdB', 'aliasB', 'cwdB', ['envB'], false);

    $fake->assertStarted(fn ($cmd, $alias, $cwd, $env, $persistent) => $alias === 'aliasA' &&
        $cmd === 'cmdA' &&
        $cwd === 'cwdA' &&
        $env === ['envA'] &&
        $persistent === true);

    $fake->assertStarted(fn ($cmd, $alias, $cwd, $env, $persistent) => $alias === 'aliasB' &&
        $cmd === 'cmdB' &&
        $cwd === 'cwdB' &&
        $env === ['envB'] &&
        $persistent === false);

    try {
        $fake->assertStarted(fn ($cmd, $alias, $cwd, $env, $persistent) => $alias === 'aliasC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts php using callable', function () {
    swap(ChildProcessContract::class, $fake = app(ChildProcessFake::class));

    $fake->php('cmdA', 'aliasA', ['envA'], true);
    $fake->php('cmdB', 'aliasB', ['envB'], false);

    $fake->assertPhp(fn ($cmd, $alias, $env, $persistent) => $alias === 'aliasA' &&
        $cmd === 'cmdA' &&
        $env === ['envA'] &&
        $persistent === true);

    $fake->assertPhp(fn ($cmd, $alias, $env, $persistent) => $alias === 'aliasB' &&
        $cmd === 'cmdB' &&
        $env === ['envB'] &&
        $persistent === false);

    try {
        $fake->assertPhp(fn ($cmd, $alias, $env, $persistent) => $alias === 'aliasC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts artisan using callable', function () {
    swap(ChildProcessContract::class, $fake = app(ChildProcessFake::class));

    $fake->artisan('cmdA', 'aliasA', ['envA'], true);
    $fake->artisan('cmdB', 'aliasB', ['envB'], false);

    $fake->assertArtisan(fn ($cmd, $alias, $env, $persistent) => $alias === 'aliasA' &&
        $cmd === 'cmdA' &&
        $env === ['envA'] &&
        $persistent === true);

    $fake->assertArtisan(fn ($cmd, $alias, $env, $persistent) => $alias === 'aliasB' &&
        $cmd === 'cmdB' &&
        $env === ['envB'] &&
        $persistent === false);

    try {
        $fake->assertArtisan(fn ($cmd, $alias, $env, $persistent) => $alias === 'aliasC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts stop using string', function () {
    swap(ChildProcessContract::class, $fake = app(ChildProcessFake::class));

    $fake->stop('testA');
    $fake->stop('testB');

    $fake->assertStop('testA');
    $fake->assertStop('testB');

    try {
        $fake->assertStop('testC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts stop using callable', function () {
    swap(ChildProcessContract::class, $fake = app(ChildProcessFake::class));

    $fake->stop('testA');
    $fake->stop('testB');

    $fake->assertStop(fn (string $alias) => $alias === 'testA');
    $fake->assertStop(fn (string $alias) => $alias === 'testB');

    try {
        $fake->assertStop(fn (string $alias) => $alias === 'testC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts restart using string', function () {
    swap(ChildProcessContract::class, $fake = app(ChildProcessFake::class));

    $fake->restart('testA');
    $fake->restart('testB');

    $fake->assertRestart('testA');
    $fake->assertRestart('testB');

    try {
        $fake->assertRestart('testC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts restart using callable', function () {
    swap(ChildProcessContract::class, $fake = app(ChildProcessFake::class));

    $fake->restart('testA');
    $fake->restart('testB');

    $fake->assertRestart(fn (string $alias) => $alias === 'testA');
    $fake->assertRestart(fn (string $alias) => $alias === 'testB');

    try {
        $fake->assertRestart(fn (string $alias) => $alias === 'testC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts message using callable', function () {
    swap(ChildProcessContract::class, $fake = app(ChildProcessFake::class));

    $fake->message('messageA', 'aliasA');
    $fake->message('messageB', 'aliasB');

    $fake->assertMessage(fn (string $message, string $alias) => $message === 'messageA' && $alias === 'aliasA');
    $fake->assertMessage(fn (string $message, string $alias) => $message === 'messageB' && $alias === 'aliasB');

    try {
        $fake->assertMessage(fn (string $message, string $alias) => $message === 'messageC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});


