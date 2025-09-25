<?php

namespace Native\Desktop;

use Native\Desktop\Client\Client;
use Native\Desktop\Menu\Menu;

class ContextMenu
{
    public function __construct(protected Client $client) {}

    public function register(Menu $menu)
    {
        $items = $menu->toArray()['submenu'];

        $this->client->post('context', [
            'entries' => $items,
        ]);
    }

    public function remove()
    {
        $this->client->delete('context');
    }
}
