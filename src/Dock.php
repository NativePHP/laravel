<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;
use Native\Laravel\Menu\Menu;

class Dock
{
    public function __construct(protected Client $client) {}

    public function menu(Menu $menu)
    {
        $items = $menu->toArray()['submenu'];

        $this->client->post('dock', [
            'items' => $items,
        ]);
    }
}
