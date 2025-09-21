<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Laravel\Menu\Menu;

/**
 * @method static void register(Menu $menu)
 * @method static void remove()
 */
class ContextMenu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\ContextMenu::class;
    }
}
