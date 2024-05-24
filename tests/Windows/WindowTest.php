<?php

use Native\Laravel\Facades\Window;

it('test window', function () {
    $window = Window::open()
        ->id('main')
        ->title('milwad')
        ->titleBarStyle('milwad')
        ->rememberState()
        ->frameless()
        ->focusable()
        ->hasShadow()
        ->alwaysOnTop()
        ->showDevTools()
        ->resizable()
        ->movable()
        ->minimizable()
        ->maximizable()
        ->closable()
        ->fullscreen()
        ->kiosk()
        ->hideMenu();

    $windowArray = $window->toArray();

    expect($windowArray['id'])->toBe('main');
    expect($windowArray['title'])->toBe('milwad');
    expect($windowArray['titleBarStyle'])->toBe('milwad');
    expect($windowArray['rememberState'])->toBeTrue();
    expect($windowArray['frame'])->toBeFalse();
    expect($windowArray['focusable'])->toBeTrue();
    expect($windowArray['hasShadow'])->toBeTrue();
    expect($windowArray['alwaysOnTop'])->toBeTrue();
    expect($windowArray['showDevTools'])->toBeTrue();
    expect($windowArray['resizable'])->toBeTrue();
    expect($windowArray['movable'])->toBeTrue();
    expect($windowArray['minimizable'])->toBeTrue();
    expect($windowArray['maximizable'])->toBeTrue();
    expect($windowArray['closable'])->toBeTrue();
    expect($windowArray['fullscreen'])->toBeFalse();
    expect($windowArray['kiosk'])->toBeFalse();
    expect($windowArray['autoHideMenuBar'])->toBeTrue();
});

it('test title bar for window', function () {
    $window = Window::open()
        ->titleBarHidden();

    expect($window->toArray()['titleBarStyle'])->toBe('hidden');

    $window->titleBarHiddenInset();

    expect($window->toArray()['titleBarStyle'])->toBe('hiddenInset');

    $window->titleBarButtonsOnHover();

    expect($window->toArray()['titleBarStyle'])->toBe('customButtonsOnHover');
});

it('test for trafficLightPosition in window', function () {
    $window = Window::open()
        ->trafficLightPosition(10, 10);

    expect($window->toArray()['trafficLightPosition'])
        ->toBeArray()
        ->toHaveKeys(['x', 'y'])
        ->toHaveLength(2)
        ->toMatchArray(['x' => 10, 'y' => 10]);

    $window->trafficLightPosition(5, 15);

    expect($window->toArray()['trafficLightPosition'])
        ->toBeArray()
        ->toHaveKeys(['x', 'y'])
        ->toHaveLength(2)
        ->toMatchArray(['x' => 5, 'y' => 15]);
});

it('test for invisibleFrameless in window', function () {
    $window = Window::open()->invisibleFrameless();
    $windowArray = $window->toArray();

    expect($windowArray['frame'])->toBeTrue();
    expect($windowArray['transparent'])->toBeTrue();
    expect($windowArray['focusable'])->toBeFalse();
    expect($windowArray['hasShadow'])->toBeFalse();
});
