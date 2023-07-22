<?php

namespace Native\Laravel\MenuBar;

use Native\Laravel\Client\Client;

class MenuBarManager
{
    /**
     * The HTTP client instance.
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Create a new MenuBarManager instance.
     *
     * @param Client $client The HTTP client instance.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a new MenuBar instance.
     *
     * @return PendingCreateMenuBar The instance for creating a new menu bar.
     */
    public function create()
    {
        return (new PendingCreateMenuBar())->setClient($this->client);
    }

    /**
     * Show the menu bar.
     *
     * @return void
     */
    public function show()
    {
        $this->client->post('menu-bar/show');
    }

    /**
     * Hide the menu bar.
     *
     * @return void
     */
    public function hide()
    {
        $this->client->post('menu-bar/close');
    }

    /**
     * Set the label to be displayed in the menu bar.
     *
     * @param string $label The label text to be set.
     * @return void
     */
    public function label(string $label)
    {
        $this->client->post('menu-bar/label', [
            'label' => $label,
        ]);
    }
}
