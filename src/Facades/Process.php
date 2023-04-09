<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Process extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Process::class;
    }
}
