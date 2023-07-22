<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;
use Native\Laravel\Menu\Menu;

class ContextMenu
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
     * Register a context menu with the specified menu items.
     *
     * @param Menu $menu The Menu instance containing the items to be displayed in the context menu.
     * @return void
     */
    public function register(Menu $menu)
    {
        $items = $menu->toArray()['submenu'];

        $this->client->post('context', [
            'entries' => $items,
        ]);
    }

    /**
     * Remove the registered context menu.
     *
     * @return void
     */
    public function remove()
    {
        $this->client->delete('context');
    }
}
