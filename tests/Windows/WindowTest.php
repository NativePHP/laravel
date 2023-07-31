<?php

it('test window', function () {
    $window = \Native\Laravel\Facades\Window::open()
        ->id('main')
        ->title('milwad')
        ->titleBarStyle('milwad')
        ->hideMenu();

    $windowArray = $window->toArray();

    expect($windowArray['id'])->toBe('main');
    expect($windowArray['title'])->toBe('milwad');
    expect($windowArray['titleBarStyle'])->toBe('milwad');
    expect($windowArray['autoHideMenuBar'])->toBeTrue();
});
