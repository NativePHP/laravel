<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Laravel\Contracts\WindowManager as WindowManagerContract;
use Native\Laravel\Fakes\WindowManagerFake;

/**
 * @method static \Native\Laravel\Windows\PendingOpenWindow open(string $id = 'main')
 * @method static void close($id = null)
 * @method static object current()
 * @method static array all()
 * @method static void resize($width, $height, $id = null)
 * @method static void position($x, $y, $animated = false, $id = null)
 * @method static void alwaysOnTop($alwaysOnTop = true, $id = null)
 * @method static void reload($id = null)
 * @method static void maximize($id = null)
 * @method static void minimize($id = null)
 */
class Window extends Facade
{
    public static function fake()
    {
        return tap(static::getFacadeApplication()->make(WindowManagerFake::class), function ($fake) {
            static::swap($fake);
        });
    }

    protected static function getFacadeAccessor()
    {
        return WindowManagerContract::class;
    }
}
