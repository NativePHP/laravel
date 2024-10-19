<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static object cursorPosition()
 * @method static array displays()
 * @method static array getCenterOfActiveScreen()
 */
class Screen extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Screen::class;
    }
}
