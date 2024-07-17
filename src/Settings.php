<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Settings
{
    public function __construct(protected Client $client) {}

    public function set($key, $value): void
    {
        $this->client->post('settings/'.$key, [
            'value' => $value,
        ]);
    }

    public function get($key, $default = null): mixed
    {
        return $this->client->get('settings/'.$key)->json('value') ?? $default;
    }
}
