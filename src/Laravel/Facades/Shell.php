<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void showInFolder(string $path)
 * @method static string openFile(string $path)
 * @method static void trashFile(string $path)
 * @method static void openExternal(string $url)
 */
class Shell extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Shell::class;
    }
}
