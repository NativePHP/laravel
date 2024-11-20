<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void create()
 */
class Menu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Menu\MenuBuilder::class;
    }
}
