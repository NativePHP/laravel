<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Laravel\Menu\Menu;

/**
 * @method static void menu(Menu $menu)
 */
class Dock extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Dock::class;
    }
}
