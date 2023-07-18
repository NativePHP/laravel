<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class GlobalShortcut extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\GlobalShortcut::class;
    }
}
