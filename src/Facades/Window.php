<?php

namespace Native\Desktop\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Desktop\Contracts\WindowManager as WindowManagerContract;
use Native\Desktop\Fakes\WindowManagerFake;

/**
 * @method static \Native\Desktop\Windows\PendingOpenWindow open(string $id = 'main')
 * @method static void close($id = null)
 * @method static object current()
 * @method static array all()
 * @method static void resize($width, $height, $id = null)
 * @method static void position($x, $y, $animated = false, $id = null)
 * @method static void alwaysOnTop($alwaysOnTop = true, $id = null)
 * @method static void reload($id = null)
 * @method static void maximize($id = null)
 * @method static void minimize($id = null)
 * @method static void zoomFactor(float $zoomFactor = 1.0)
 * @method static void preventLeaveDomain(bool $preventLeaveDomain = true)
 * @method static void preventLeavePage(bool $preventLeavePage = true): self
 * @method static void suppressNewWindows()
 * @method static void webPreferences(array $preferences)
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
