<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Laravel\Menu\Menu;

/**
 * @method static void bounce(string $type = 'informational')
 * @method static void|string badge(?string $type = null)
 * @method static void cancelBounce()
 * @method static void hide()
 * @method static void icon(string $Path)
 * @method static void menu(Menu $menu)
 * @method static void show()
 */
class Dock extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Dock::class;
    }
}
