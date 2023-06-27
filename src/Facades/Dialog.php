<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Dialog extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Dialogs\DialogManager::class;
    }
}
