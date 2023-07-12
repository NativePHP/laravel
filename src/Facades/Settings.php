<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Settings extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Settings::class;
    }
}
