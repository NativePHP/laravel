<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Shell extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Shell::class;
    }
}
