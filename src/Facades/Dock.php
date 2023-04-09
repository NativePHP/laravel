<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Dock extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Dock::class;
    }
}
