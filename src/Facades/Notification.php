<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Notification extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Notification::class;
    }
}
