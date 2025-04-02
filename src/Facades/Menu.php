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
 * @method static Role about(?string $label = null)
 * @method static Role file(?string $label = null)
 * @method static Role edit(?string $label = null)
 * @method static Role view(?string $label = null)
 * @method static Role window(?string $label = null)
 * @method static Role help(?string $label = null)
 * @method static Role fullscreen(?string $label = null)
 * @method static Role separator()
 * @method static Role devTools(?string $label = null)
 * @method static Role undo(?string $label = null)
 * @method static Role redo(?string $label = null)
 * @method static Role cut(?string $label = null)
 * @method static Role copy(?string $label = null)
 * @method static Role paste(?string $label = null)
 * @method static Role pasteAndMatchStyle(?string $label = null)
 * @method static Role reload(?string $label = null)
 * @method static Role minimize(?string $label = null)
 * @method static Role close(?string $label = null)
 * @method static Role quit(?string $label = null)
 * @method static Role hide(?string $label = null)
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
