<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Settings
{
    public function __construct(protected Client $client) {}

    public function set(string $key, $value): void
    {
        $this->client->post('settings/'.$key, [
            'value' => $value,
        ]);
    }

    public function get(string $key, $default = null): mixed
    {
        return $this->client->get('settings/'.$key)->json('value') ?? $default;
    }

    public function forget(string $key): void
    {
        $this->client->delete('settings/'.$key);
    }

    public function clear(): void
    {
        $this->client->delete('settings/');
    }
}
