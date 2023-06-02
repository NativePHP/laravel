<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Window extends Facade
{
    protected static function getFacadeAccessor()
    {
        self::clearResolvedInstance(\Native\Laravel\Window::class);

        return \Native\Laravel\Window::class;
    }
}
