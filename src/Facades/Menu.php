<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Laravel\Contracts\MenuItem;
use Native\Laravel\Menu\Items\Checkbox;
use Native\Laravel\Menu\Items\Label;
use Native\Laravel\Menu\Items\Link;
use Native\Laravel\Menu\Items\Radio;
use Native\Laravel\Menu\Items\Role;
use Native\Laravel\Menu\Items\Separator;

/**
 * @method static \Native\Laravel\Menu\Menu make(MenuItem ...$items)
 * @method static Checkbox checkbox(string $label, bool $checked = false, ?string $hotkey = null)
 * @method static Label label(string $label)
 * @method static Link link(string $url, string $label = null, ?string $hotkey = null)
 * @method static Link route(string $url, string $label = null, ?string $hotkey = null)
 * @method static Radio radio(string $label, bool $checked = false, ?string $hotkey = null)
 * @method static Role app()
 * @method static Role file()
 * @method static Role edit()
 * @method static Role view()
 * @method static Role window()
 * @method static Role help()
 * @method static Role fullscreen()
 * @method static Role separator()
 * @method static Role devTools()
 * @method static Role undo()
 * @method static Role redo()
 * @method static Role cut()
 * @method static Role copy()
 * @method static Role paste()
 * @method static Role pasteAndMatchStyle()
 * @method static Role reload()
 * @method static Role minimize()
 * @method static Role close()
 * @method static Role quit()
 * @method static Role hide()
 * @method static void create(MenuItem ...$items)
 * @method static void default()
 */
class Menu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Menu\MenuBuilder::class;
    }
}
