<?php

namespace Native\Laravel\Menu;

use Native\Laravel\Client\Client;
use Native\Laravel\Contracts\MenuItem;
use Native\Laravel\Enums\RolesEnum;

class MenuBuilder
{
    public function __construct(protected Client $client) {}

    public function make(MenuItem ...$items): Menu
    {
        $menu = new Menu($this->client);

        foreach ($items as $item) {
            $menu->add($item);
        }

        return $menu;
    }

    public function create(MenuItem ...$items): void
    {
        $this->make(...$items)
            ->register();
    }

    public function default(): void
    {
        $this->create(
            $this->app(),
            $this->file(),
            $this->edit(),
            $this->view(),
            $this->window(),
        );
    }

    public function label(): Items\Label
    {
        return new Items\Label($label);
    }

    public function goToUrl(string $url, ?string $label = null, ?string $hotkey = null): Items\GoToUrl
    {
        return new Items\GoToUrl($url, $label, $hotkey);
    }

    public function goToRoute(string $route, ?string $label = null, ?string $hotkey = null): Items\GoToUrl
    {
        return new Items\GoToUrl(route($route), $label, $hotkey);
    }

    public function checkbox(string $label, bool $checked = false, ?string $hotkey = null): Items\Checkbox
    {
        return new Items\Checkbox($label, $checked, $hotkey);
    }

    public function event(string $event, ?string $label = null, ?string $hotkey = null): Items\Event
    {
        return new Items\Event($event, $label, $hotkey);
    }

    public function link(string $url, ?string $label = null, ?string $hotkey = null): Items\Link
    {
        return new Items\Link($url, $label, $hotkey);
    }

    public function app(): Items\Role
    {
        return new Items\Role(RolesEnum::APP_MENU);
    }

    public function file($label = 'File'): Items\Role
    {
        return new Items\Role(RolesEnum::FILE_MENU, $label);
    }

    public function edit($label = 'Edit'): Items\Role
    {
        return new Items\Role(RolesEnum::EDIT_MENU, $label);
    }

    public function view($label = 'View'): Items\Role
    {
        return new Items\Role(RolesEnum::VIEW_MENU, $label);
    }

    public function window($label = 'Window'): Items\Role
    {
        return new Items\Role(RolesEnum::WINDOW_MENU, $label);
    }

    public function separator(): Items\Separator
    {
        return new Items\Separator;
    }

    public function fullscreen(): Items\Role
    {
        return new Items\Role(RolesEnum::TOGGLE_FULL_SCREEN);
    }

    public function devTools(): Items\Role
    {
        return new Items\Role(RolesEnum::TOGGLE_DEV_TOOLS);
    }

    public function undo(): Items\Role
    {
        return new Items\Role(RolesEnum::UNDO);
    }

    public function redo(): Items\Role
    {
        return new Items\Role(RolesEnum::REDO);
    }

    public function cut(): Items\Role
    {
        return new Items\Role(RolesEnum::CUT);
    }

    public function copy(): Items\Role
    {
        return new Items\Role(RolesEnum::COPY);
    }

    public function paste(): Items\Role
    {
        return new Items\Role(RolesEnum::PASTE);
    }

    public function pasteAndMatchStyle(): Items\Role
    {
        return new Items\Role(RolesEnum::PASTE_STYLE);
    }

    public function reload(): Items\Role
    {
        return new Items\Role(RolesEnum::RELOAD);
    }

    public function minimize(): Items\Role
    {
        return new Items\Role(RolesEnum::MINIMIZE);
    }

    public function close(): Items\Role
    {
        return new Items\Role(RolesEnum::PASTE_STYLE);
    }

    public function quit(): Items\Role
    {
        return new Items\Role(RolesEnum::QUIT);
    }
}
