<?php

use Illuminate\Support\Facades\Http;
use Native\Laravel\Contracts\WindowManager as WindowManagerContract;
use Native\Laravel\Facades\Window;
use Native\Laravel\Fakes\WindowManagerFake;
use Native\Laravel\Windows\PendingOpenWindow;
use Native\Laravel\Windows\Window as WindowClass;
use PHPUnit\Framework\AssertionFailedError;
use Webmozart\Assert\InvalidArgumentException;

use function Pest\Laravel\swap;

it('swaps implementations using facade', function () {
    Window::fake();

    expect(app(WindowManagerContract::class))->toBeInstanceOf(WindowManagerFake::class);
});

it('asserts that a window was opened', function () {
    Http::fake(['*' => Http::response(status: 200)]);

    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    $fake->alwaysReturnWindows([
        new PendingOpenWindow('doesnt-matter'),
    ]);
    app(WindowManagerContract::class)->open('main');
    app(WindowManagerContract::class)->open('secondary');

    $fake->assertOpened('main');
    $fake->assertOpened('secondary');

    try {
        $fake->assertOpened('tertiary');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts that a window was opened using callable', function () {
    Http::fake(['*' => Http::response(status: 200)]);

    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    $fake->alwaysReturnWindows([
        new PendingOpenWindow('doesnt-matter'),
    ]);

    app(WindowManagerContract::class)->open('main');
    app(WindowManagerContract::class)->open('secondary');

    $fake->assertOpened(fn (string $id) => $id === 'main');
    $fake->assertOpened(fn (string $id) => $id === 'secondary');

    try {
        $fake->assertOpened(fn (string $id) => $id === 'tertiary');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts that a window was closed', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    app(WindowManagerContract::class)->close('main');
    app(WindowManagerContract::class)->close('secondary');

    $fake->assertClosed('main');
    $fake->assertClosed('secondary');

    try {
        $fake->assertClosed('tertiary');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts that a window was closed using callable', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    app(WindowManagerContract::class)->close('main');
    app(WindowManagerContract::class)->close('secondary');

    $fake->assertClosed(fn (string $id) => $id === 'main');
    $fake->assertClosed(fn (string $id) => $id === 'secondary');

    try {
        $fake->assertClosed(fn (string $id) => $id === 'tertiary');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts that a window was hidden', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    app(WindowManagerContract::class)->hide('main');
    app(WindowManagerContract::class)->hide('secondary');

    $fake->assertHidden('main');
    $fake->assertHidden('secondary');

    try {
        $fake->assertHidden('tertiary');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts that a window was hidden using callable', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    app(WindowManagerContract::class)->hide('main');
    app(WindowManagerContract::class)->hide('secondary');

    $fake->assertHidden(fn (string $id) => $id === 'main');
    $fake->assertHidden(fn (string $id) => $id === 'secondary');

    try {
        $fake->assertHidden(fn (string $id) => $id === 'tertiary');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts that a window was shown', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    app(WindowManagerContract::class)->show('main');
    app(WindowManagerContract::class)->show('secondary');

    $fake->assertShown('main');
    $fake->assertShown('secondary');

    try {
        $fake->assertShown('tertiary');
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts opened count', function () {
    Http::fake(['*' => Http::response(status: 200)]);

    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    $fake->alwaysReturnWindows([
        new PendingOpenWindow('doesnt-matter'),
    ]);

    app(WindowManagerContract::class)->open('main');
    app(WindowManagerContract::class)->open();
    app(WindowManagerContract::class)->open();

    $fake->assertOpenedCount(3);

    try {
        $fake->assertOpenedCount(4);
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts closed count', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    app(WindowManagerContract::class)->close('main');
    app(WindowManagerContract::class)->close();
    app(WindowManagerContract::class)->close();

    $fake->assertClosedCount(3);

    try {
        $fake->assertClosedCount(4);
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts hidden count', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    app(WindowManagerContract::class)->hide('main');
    app(WindowManagerContract::class)->hide();
    app(WindowManagerContract::class)->hide();

    $fake->assertHiddenCount(3);

    try {
        $fake->assertHiddenCount(4);
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts shown count', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    app(WindowManagerContract::class)->show('main');
    app(WindowManagerContract::class)->show();
    app(WindowManagerContract::class)->show();

    $fake->assertShownCount(3);

    try {
        $fake->assertShownCount(4);
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('forces the return value of current window', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    $fake->alwaysReturnWindows($windows = [
        new WindowClass('testA'),
        new WindowClass('testB'),
    ]);

    expect($windows)->toContain(app(WindowManagerContract::class)->current());
});

it('forces the return value of all windows', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    $fake->alwaysReturnWindows($windows = [
        new WindowClass('testA'),
        new WindowClass('testB'),
    ]);

    expect(app(WindowManagerContract::class)->all())->toBe($windows);
});

it('forces the return value of a specific window', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    $fake->alwaysReturnWindows($windows = [
        new WindowClass('testA'),
        new WindowClass('testB'),
    ]);

    expect(app(WindowManagerContract::class)->get('testA'))->toBe($windows[0]);
    expect(app(WindowManagerContract::class)->get('testB'))->toBe($windows[1]);
});

test('that the get method throws an exception if multiple matching window ids exist', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    $fake->alwaysReturnWindows($windows = [
        new WindowClass('testA'),
        new WindowClass('testA'),
    ]);

    app(WindowManagerContract::class)->get('testA');
})->throws(InvalidArgumentException::class);

test('that the get method throws an exception if no matching window id exists', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    $fake->alwaysReturnWindows($windows = [
        new WindowClass('testA'),
    ]);

    app(WindowManagerContract::class)->get('testB');
})->throws(InvalidArgumentException::class);

test('that the current method throws an exception if no forced window return values are provided', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    app(WindowManagerContract::class)->current();
})->throws(InvalidArgumentException::class);

test('that the all method throws an exception if no forced window return values are provided', function () {
    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    app(WindowManagerContract::class)->all();
})->throws(InvalidArgumentException::class);

test('that the open method throws an exception if no forced window return values are provided', function () {
    Http::fake([
        '*' => Http::response(status: 200),
    ]);

    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    app(WindowManagerContract::class)->open('test');
})->throws(InvalidArgumentException::class);

test('that the open method throws an exception if multiple matching window ids exist', function () {
    Http::fake([
        '*' => Http::response(status: 200),
    ]);

    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    $fake->alwaysReturnWindows($windows = [
        new WindowClass('testA'),
        new WindowClass('testA'),
    ]);

    app(WindowManagerContract::class)->open('testA');
})->throws(InvalidArgumentException::class);

test('that the open method returns a random window if none match the id provided', function () {
    Http::fake([
        '*' => Http::response(status: 200),
    ]);

    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    $fake->alwaysReturnWindows($windows = [
        new PendingOpenWindow('testA'),
    ]);

    expect($windows)->toContain(app(WindowManagerContract::class)->open('testC'));
});

test('that the open method returns a window if a matching window id exists', function () {
    Http::fake([
        '*' => Http::response(status: 200),
    ]);

    swap(WindowManagerContract::class, $fake = app(WindowManagerFake::class));

    $fake->alwaysReturnWindows($windows = [
        new PendingOpenWindow('testA'),
    ]);

    expect(app(WindowManagerContract::class)->open('testA'))->toBe($windows[0]);
});
