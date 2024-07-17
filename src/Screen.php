<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Screen
{
    public function __construct(protected Client $client) {}

    public function cursorPosition(): object
    {
        return (object) $this->client->get('screen/cursor-position')->json();
    }

    public function displays(): array
    {
        return $this->client->get('screen/displays')->json('displays');
    }
}
