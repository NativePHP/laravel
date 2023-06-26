<?php

namespace Native\Laravel\MenuBar;

use Native\Laravel\Client\Client;
use Native\Laravel\Concerns\DetectsWindowId;

class MenuBarManager
{
    use DetectsWindowId;

    public function __construct(protected Client $client)
    {

    }

    public function create(string $id = 'menubar')
    {
        return (new PendingCreateMenuBar($id))->setClient($this->client);
    }

    public function close($id = 'menubar')
    {
        $this->client->post('menu-bar/close', [
            'id' => $id ?? $this->detectId(),
        ]);
    }

    public function label(string $label)
    {
        $this->client->post('menu-bar/label', [
            'label' => $label,
        ]);
    }
}
