<?php

use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Menu\Menu;

it('menubar with create', function () {
    config()->set('nativephp-internal.api_url', 'https://jsonplaceholder.typicode.com/todos/1');

    $menuBar = MenuBar::create()
        ->showDockIcon()
        ->alwaysOnTop()
        ->label('milwad')
        ->icon('nativephp.png')
        ->url('https://github.com/milwad-dev')
        ->withContextMenu(
            Menu::new()->label('My Application')->quit(),
        );
    $menuBarArray = $menuBar->toArray();

    $this->assertTrue($menuBarArray['showDockIcon']);
    $this->assertTrue($menuBarArray['alwaysOnTop']);
    $this->assertEquals('milwad', $menuBarArray['label']);
    $this->assertEquals('https://github.com/milwad-dev', $menuBarArray['url']);
    $this->assertEquals('nativephp.png', $menuBarArray['icon']);
    $this->assertEquals('trayCenter', $menuBarArray['windowPosition']);
    $this->assertIsArray($menuBarArray['contextMenu']);
});
