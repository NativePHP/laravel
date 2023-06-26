<?php

namespace Native\Laravel\MenuBar;

use Native\Laravel\Client\Client;
use Native\Laravel\Concerns\HasDimensions;
use Native\Laravel\Concerns\HasVibrancy;
use Native\Laravel\Menu\Menu;

class MenuBar
{
    use HasVibrancy;
    use HasDimensions;

    protected string $url = '';

    protected string $icon = '';

    protected Menu $contextMenu;

    protected bool $alwaysOnTop = false;

    protected bool $showDockIcon = false;

    protected string $id;

    protected Client $client;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->url = url('/');
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function showDockIcon($value = true): self
    {
        $this->showDockIcon = $value;

        return $this;
    }

    public function alwaysOnTop($alwaysOnTop = true): self
    {
        $this->alwaysOnTop = $alwaysOnTop;

        return $this;
    }

    public function withContextMenu(Menu $menu): self
    {
        $this->contextMenu = $menu;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'icon' => $this->icon,
            'x' => $this->x,
            'y' => $this->y,
            'width' => $this->width,
            'height' => $this->height,
            'vibrancy' => $this->vibrancy,
            'showDockIcon' => $this->showDockIcon,
            'transparency' => $this->transparent,
            'backgroundColor' => $this->backgroundColor,
            'contextMenu' => $this->contextMenu ? $this->contextMenu->toArray()['submenu'] : null,
            'alwaysOnTop' => $this->alwaysOnTop,
        ];
    }
}
