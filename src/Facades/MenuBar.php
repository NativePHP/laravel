<?php

namespace Native\Desktop\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Desktop\Menu\Menu;

/**
 * @method static \Native\Desktop\MenuBar\PendingCreateMenuBar create()
 * @method static void show()
 * @method static void hide()
 * @method static void label(string $label)
 * @method static void tooltip(string $label)
 * @method static void icon(string $icon)
 * @method static void resize(int $width, int $height)
 * @method static void contextMenu(Menu $contextMenu)
 * @method static void webPreferences(array $preferences)
 */
class MenuBar extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Desktop\MenuBar\MenuBarManager::class;
    }
}
