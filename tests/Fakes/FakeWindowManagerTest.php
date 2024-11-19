<?php

use Native\Laravel\Contracts\WindowManager as WindowManagerContract;
use Native\Laravel\Facades\Window;
use Native\Laravel\Fakes\WindowManagerFake;
use Native\Laravel\Windows\Window as WindowClass;
use PHPUnit\Framework\AssertionFailedError;

use function Pest\Laravel\swap;

it('swaps implementations using facade', function () {
    Window::fake();

    expect(app(WindowManagerContract::class))->toBeInstanceOf(WindowManagerFake::class);
});

it('asserts that a window was opened', function () {
    swap(WindowManagerContract::class, $fake = new WindowManagerFake);

    app(WindowManagerContract::class)->open('main');
    app(WindowManagerContract::class)->open('secondary');

    $fake->assertOpened('main');
    $fake->assertOpened('secondary');

    try {
        $fake->assertOpened('tertiary');
    } catch (AssertionFailedError) {
        expect(true)->toBeTrue();

        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts that a window was closed', function () {
    swap(WindowManagerContract::class, $fake = new WindowManagerFake);

    app(WindowManagerContract::class)->close('main');
    app(WindowManagerContract::class)->close('secondary');

    $fake->assertClosed('main');
    $fake->assertClosed('secondary');

    try {
        $fake->assertClosed('tertiary');
    } catch (AssertionFailedError) {
        expect(true)->toBeTrue();

        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts that a window was hidden', function () {
    swap(WindowManagerContract::class, $fake = new WindowManagerFake);

    app(WindowManagerContract::class)->hide('main');
    app(WindowManagerContract::class)->hide('secondary');

    $fake->assertHidden('main');
    $fake->assertHidden('secondary');

    try {
        $fake->assertHidden('tertiary');
    } catch (AssertionFailedError) {
        expect(true)->toBeTrue();

        return;
    }

    $this->fail('Expected assertion to fail');
});

it('forces the return value of current window', function () {
    swap(WindowManagerContract::class, $fake = new WindowManagerFake);

    $fake->alwaysReturnWindows($windows = [
        new WindowClass('testA'),
        new WindowClass('testB'),
    ]);

    expect($windows)->toContain(app(WindowManagerContract::class)->current());
});

it('forces the return value of all windows', function () {
    swap(WindowManagerContract::class, $fake = new WindowManagerFake);

    $fake->alwaysReturnWindows($windows = [
        new WindowClass('testA'),
        new WindowClass('testB'),
    ]);

    expect(app(WindowManagerContract::class)->all())->toBe($windows);
});

it('forces the return value of a specific window', function () {
    swap(WindowManagerContract::class, $fake = new WindowManagerFake);

    $fake->alwaysReturnWindows($windows = [
        new WindowClass('testA'),
        new WindowClass('testB'),
    ]);

    expect(app(WindowManagerContract::class)->get('testA'))->toBe($windows[0]);
    expect(app(WindowManagerContract::class)->get('testB'))->toBe($windows[1]);
});

test('that the get method throws an exception if multiple matching window ids exist', function () {
    swap(WindowManagerContract::class, $fake = new WindowManagerFake);

    $fake->alwaysReturnWindows($windows = [
        new WindowClass('testA'),
        new WindowClass('testA'),
    ]);

    app(WindowManagerContract::class)->get('testA');
})->throws(AssertionFailedError::class);

test('that the get method throws an exception if no matching window id exists', function () {
    swap(WindowManagerContract::class, $fake = new WindowManagerFake);

    $fake->alwaysReturnWindows($windows = [
        new WindowClass('testA'),
    ]);

    app(WindowManagerContract::class)->get('testB');
})->throws(AssertionFailedError::class);

test('that the current method throws an exception if no forced window return values are provided', function () {
    swap(WindowManagerContract::class, $fake = new WindowManagerFake);

    app(WindowManagerContract::class)->current();
})->throws(AssertionFailedError::class);

test('that the all method throws an exception if no forced window return values are provided', function () {
    swap(WindowManagerContract::class, $fake = new WindowManagerFake);

    app(WindowManagerContract::class)->all();
})->throws(AssertionFailedError::class);
