<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Screen extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Screen::class;
    }
}
