<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Laravel\Menu\Menu;

/**
 * @method static \Native\Laravel\MenuBar\PendingCreateMenuBar create()
 * @method static void show()
 * @method static void hide()
 * @method static void label(string $label)
 * @method static void contextMenu(Menu $contextMenu)
 */
class MenuBar extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\MenuBar\MenuBarManager::class;
    }
}
