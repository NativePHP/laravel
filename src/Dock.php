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

    public function show()
    {
        $this->client->post('dock/show');
    }

    public function hide()
    {
        $this->client->post('dock/hide');
    }

    public function icon(string $path)
    {
        $this->client->post('dock/icon', ['path' => $path]);
    }

    public function bounce(string $type = 'informational')
    {
        $this->client->post('dock/bounce', ['type' => $type]);
    }

    public function cancelBounce()
    {
        $this->client->post('dock/cancel-bounce');
    }

    public function badge(?string $label = null): ?string
    {
        if (is_null($label)) {
            return $this->client->get('dock/badge');
        }

        $this->client->post('dock/badge', ['label' => $label]);

        return null;
    }
}
