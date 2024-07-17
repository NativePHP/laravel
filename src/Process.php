<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Process
{
    public function __construct(protected Client $client) {}

    public function arch(): string
    {
        return $this->fresh()->arch;
    }

    public function platform(): string
    {
        return $this->fresh()->platform;
    }

    public function uptime(): float
    {
        return $this->fresh()->uptime;
    }

    public function fresh(): object
    {
        return (object) $this->client->get('process')->json();
    }
}
