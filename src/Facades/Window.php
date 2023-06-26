<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Window extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Windows\WindowManager::class;
    }
}
