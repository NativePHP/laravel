<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class MenuBar extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\MenuBar\MenuBarManager::class;
    }
}
