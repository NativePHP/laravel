<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class System extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\System::class;
    }
}
