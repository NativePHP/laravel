<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Native\Laravel\Windows\PendingOpenWindow open(string $id = 'main')
 * @method static void close($id = null)
 * @method static object current()
 * @method static void resize($width, $height, $id = null)
 * @method static void position($x, $y, $animated = false, $id = null)
 * @method static void alwaysOnTop($alwaysOnTop = true, $id = null)
 */
class Window extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Windows\WindowManager::class;
    }
}
