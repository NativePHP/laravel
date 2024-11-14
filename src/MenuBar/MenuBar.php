<?php

namespace Native\Laravel\MenuBar;

use Native\Laravel\Client\Client;
use Native\Laravel\Concerns\HasDimensions;
use Native\Laravel\Concerns\HasPositioner;
use Native\Laravel\Concerns\HasUrl;
use Native\Laravel\Concerns\HasVibrancy;
use Native\Laravel\Menu\Menu;

class MenuBar
{
    use HasDimensions;
    use HasPositioner;
    use HasUrl;
    use HasVibrancy;

    protected string $icon = '';

    protected string $label = '';

    protected string $tooltip = '';

    protected bool $resizable = true;

    protected bool $onlyShowContextMenu = false;

    protected ?Menu $contextMenu = null;

    protected bool $alwaysOnTop = false;

    protected ?string $event = null;

    protected bool $showDockIcon = false;

    protected Client $client;

    public function __construct()
    {
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

    public function onlyShowContextMenu(bool $onlyContextMenu = true): self
    {
        $this->onlyShowContextMenu = $onlyContextMenu;

        return $this;
    }

    public function showDockIcon($value = true): self
    {
        $this->showDockIcon = $value;

        return $this;
    }

    public function label(string $label = ''): self
    {
        $this->label = $label;

        return $this;
    }

    public function tooltip(string $tooltip = ''): self
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function resizable(bool $resizable = true): static
    {
        $this->resizable = $resizable;

        return $this;
    }

    public function alwaysOnTop($alwaysOnTop = true): self
    {
        $this->alwaysOnTop = $alwaysOnTop;

        return $this;
    }

    public function event(string $event): self
    {
        $this->event = $event;

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
            'url' => $this->url,
            'icon' => $this->icon,
            'windowPosition' => $this->windowPosition,
            'x' => $this->x,
            'y' => $this->y,
            'label' => $this->label,
            'tooltip' => $this->tooltip,
            'resizable' => $this->resizable,
            'width' => $this->width,
            'height' => $this->height,
            'vibrancy' => $this->vibrancy,
            'showDockIcon' => $this->showDockIcon,
            'transparency' => $this->transparent,
            'backgroundColor' => $this->backgroundColor,
            'onlyShowContextMenu' => $this->onlyShowContextMenu,
            'contextMenu' => ! is_null($this->contextMenu) ? $this->contextMenu->toArray()['submenu'] : null,
            'alwaysOnTop' => $this->alwaysOnTop,
            'event' => $this->event,
        ];
    }
}
