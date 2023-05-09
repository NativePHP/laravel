<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Clipboard extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Clipboard::class;
    }
}
