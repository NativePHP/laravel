<?php

namespace Native\Desktop\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Desktop\Menu\Menu;

/**
 * @method static void register(Menu $menu)
 * @method static void remove()
 */
class ContextMenu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Desktop\ContextMenu::class;
    }
}
