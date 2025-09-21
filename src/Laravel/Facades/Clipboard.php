<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void clear()
 * @method static string text($text = null)
 * @method static string html($html = null)
 * @method static string|null image($image = null)
 */
class Clipboard extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Clipboard::class;
    }
}
