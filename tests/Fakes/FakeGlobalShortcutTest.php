<?php

use Native\Laravel\Facades\GlobalShortcut;
use Native\Laravel\Contracts\GlobalShortcut as GlobalShortcutContract;
use Native\Laravel\Fakes\GlobalShortcutFake;

use PHPUnit\Framework\AssertionFailedError;

use function Pest\Laravel\swap;

it('swaps implementations using facade', function () {
    GlobalShortcut::fake();

    expect(app(GlobalShortcutContract::class))->toBeInstanceOf(GlobalShortcutFake::class);
});

it('asserts key using string', function () {
    swap(GlobalShortcutContract::class, $fake = app(GlobalShortcutFake::class));

    $fake->key('testA');
    $fake->key('testB');

    $fake->assertKey('testA');
    $fake->assertKey('testB');

    try {
        $fake->assertKey('testC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts key using callable', function () {
    swap(GlobalShortcutContract::class, $fake = app(GlobalShortcutFake::class));

    $fake->key('testA');
    $fake->key('testB');

    $fake->assertKey(fn (string $key) => $key === 'testA');
    $fake->assertKey(fn (string $key) => $key === 'testB');

    try {
        $fake->assertKey(fn (string $key) => $key === 'testC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts event using string', function () {
    swap(GlobalShortcutContract::class, $fake = app(GlobalShortcutFake::class));

    $fake->event('testA');
    $fake->event('testB');

    $fake->assertEvent('testA');
    $fake->assertEvent('testB');

    try {
        $fake->assertEvent('testC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts event using callable', function () {
    swap(GlobalShortcutContract::class, $fake = app(GlobalShortcutFake::class));

    $fake->event('testA');
    $fake->event('testB');

    $fake->assertEvent(fn (string $event) => $event === 'testA');
    $fake->assertEvent(fn (string $event) => $event === 'testB');

    try {
        $fake->assertEvent(fn (string $event) => $event === 'testC');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts registered count', function () {
    swap(GlobalShortcutContract::class, $fake = app(GlobalShortcutFake::class));

    $fake->register();
    $fake->register();
    $fake->register();

    $fake->assertRegisteredCount(3);

    try {
        $fake->assertRegisteredCount(2);
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts unregistered count', function () {
    swap(GlobalShortcutContract::class, $fake = app(GlobalShortcutFake::class));

    $fake->unregister();
    $fake->unregister();
    $fake->unregister();

    $fake->assertUnregisteredCount(3);

    try {
        $fake->assertUnregisteredCount(2);
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

