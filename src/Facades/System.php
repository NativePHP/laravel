<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool canPromptTouchID()
 * @method static bool promptTouchID(string $reason)
 * @method static bool canEncrypt()
 * @method static string encrypt(string $string)
 * @method static string decrypt(string $string)
 * @method static array printers()
 * @method static void print(string $html, ?\Native\Laravel\DataObjects\Printer $printer = null)
 * @method static string printToPDF(string $reason)
 * @method static string timezone()
 */
class System extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\System::class;
    }
}
