<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool canPromptTouchID()
 * @method static bool promptTouchID(string $reason)
 * @method static array printers()
 * @method static void print(string $html, ?\Native\Laravel\DataObjects\Printer $printer = null)
 * @method static string printToPDF(string $html)
 */
class System extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\System::class;
    }
}
