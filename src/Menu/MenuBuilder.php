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

    public function label(string $label, ?string $hotkey = null): Items\Label
    {
        return new Items\Label($label, $hotkey);
    }

    public function checkbox(string $label, bool $checked = false, ?string $hotkey = null): Items\Checkbox
    {
        return new Items\Checkbox($label, $checked, $hotkey);
    }

    public function radio(string $label, bool $checked = false, ?string $hotkey = null): Items\Radio
    {
        return new Items\Radio($label, $checked, $hotkey);
    }

    public function link(string $url, ?string $label = null, ?string $hotkey = null): Items\Link
    {
        return new Items\Link($url, $label, $hotkey);
    }

    public function route(string $route, ?string $label = null, ?string $hotkey = null): Items\Link
    {
        return new Items\Link(route($route), $label, $hotkey);
    }

    public function app(): Items\Role
    {
        return new Items\Role(RolesEnum::APP_MENU);
    }

    public function file(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::FILE_MENU, $label);
    }

    public function edit(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::EDIT_MENU, $label);
    }

    public function view(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::VIEW_MENU, $label);
    }

    public function window(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::WINDOW_MENU, $label);
    }

    public function separator(): Items\Separator
    {
        return new Items\Separator;
    }

    public function fullscreen(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::TOGGLE_FULL_SCREEN, $label);
    }

    public function devTools(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::TOGGLE_DEV_TOOLS, $label);
    }

    public function undo(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::UNDO, $label);
    }

    public function redo(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::REDO, $label);
    }

    public function cut(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::CUT, $label);
    }

    public function copy(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::COPY, $label);
    }

    public function paste(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::PASTE, $label);
    }

    public function pasteAndMatchStyle(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::PASTE_STYLE, $label);
    }

    public function reload(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::RELOAD, $label);
    }

    public function minimize(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::MINIMIZE, $label);
    }

    public function close(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::CLOSE, $label);
    }

    public function quit(?string $label = null): Items\Role
    {
        if (is_null($label)) {
            $label = __('Quit').' '.config('app.name');
        }

        return new Items\Role(RolesEnum::QUIT, $label);
    }

    public function help(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::HELP, $label);
    }

    public function hide(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::HIDE, $label);
    }

    public function about(?string $label = null): Items\Role
    {
        return new Items\Role(RolesEnum::ABOUT, $label);
    }
}
