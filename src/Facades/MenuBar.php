<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Native\Laravel\MenuBar\PendingCreateMenuBar create()
 * @method static void show()
 * @method static void hide()
 * @method static void label(string $label)
 */
class MenuBar extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\MenuBar\MenuBarManager::class;
    }
}
