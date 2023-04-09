<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class ContextMenu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\ContextMenu::class;
    }
}
