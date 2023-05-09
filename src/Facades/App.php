<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class App extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\App::class;
    }
}
