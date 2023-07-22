<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void focus()
 * @method static void hide()
 * @method static bool isHidden()
 * @method static string version()
 * @method static int badgeCount($count = null)
 * @method static void addRecentDocument(string $path)
 * @method static array recentDocuments()
 * @method static void clearRecentDocuments()
 */
class App extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\App::class;
    }
}
