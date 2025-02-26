<?php

namespace Native\Laravel\MenuBar;

use Native\Laravel\Client\Client;
use Native\Laravel\Menu\Menu;

class MenuBarManager
{
    public function __construct(protected Client $client) {}

    public function create()
    {
        return (new PendingCreateMenuBar)->setClient($this->client);
    }

    public function show()
    {
        $this->client->post('menu-bar/show');
    }

    public function hide()
    {
        $this->client->post('menu-bar/close');
    }

    public function label(string $label)
    {
        $this->client->post('menu-bar/label', [
            'label' => $label,
        ]);
    }

    public function tooltip(string $tooltip)
    {
        $this->client->post('menu-bar/tooltip', [
            'tooltip' => $tooltip,
        ]);
    }

    public function icon(string $icon)
    {
        $this->client->post('menu-bar/icon', [
            'icon' => $icon,
        ]);
    }

    public function resize(int $width, int $height)
    {
        $this->client->post('menu-bar/resize', [
            'width' => $width,
            'height' => $height,
        ]);
    }

    public function contextMenu(Menu $contextMenu)
    {
        $this->client->post('menu-bar/context-menu', [
            'contextMenu' => $contextMenu->toArray()['submenu'],
        ]);
    }
}
