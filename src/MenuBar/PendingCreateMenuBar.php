<?php

namespace Native\Laravel\MenuBar;

class PendingCreateMenuBar extends MenuBar
{
    /**
     * Create a new menu bar using the provided configuration.
     * The menu bar will be created when the instance is destructed.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->create();
    }

    /**
     * Create the menu bar using the configuration settings.
     *
     * @return void
     */
    protected function create(): void
    {
        $this->client->post('menu-bar/create', $this->toArray());
    }
}
