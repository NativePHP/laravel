<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class GlobalShortcut
{
    protected string $key;
    protected string $event;

    public function __construct(protected Client $client)
    {
    }

    public static function new()
    {
        return new static(new Client());
    }

    public function key(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function event(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function register(): void
    {
        $this->client->post('global-shortcut', [
            'key' => $this->key,
            'event' => $this->event,
        ]);
    }
}
