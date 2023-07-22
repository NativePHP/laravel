<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;
use Native\Laravel\Menu\Menu;

class Dock
{
    /**
     * Constructor.
     *
     * @param Client $client The HTTP client instance.
     */
    public function __construct(protected Client $client)
    {
    }

    /**
     * Set the menu items for the dock.
     *
     * @param Menu $menu The Menu instance containing the items to be displayed in the dock.
     * @return void
     */
    public function menu(Menu $menu)
    {
        $items = $menu->toArray()['submenu'];

        $this->client->post('dock', [
            'items' => $items,
        ]);
    }
}
