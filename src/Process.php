<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Process
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
     * Get the architecture of the current process.
     *
     * @return string The architecture of the process (e.g., x64, x86).
     */
    public function arch(): string
    {
        return $this->fresh()->arch;
    }

    /**
     * Get the platform of the current process.
     *
     * @return string The platform of the process (e.g., win32, linux).
     */
    public function platform(): string
    {
        return $this->fresh()->platform;
    }

    /**
     * Get the uptime of the current process.
     *
     * @return float The uptime of the process in seconds.
     */
    public function uptime(): float
    {
        return $this->fresh()->uptime;
    }

    /**
     * Get fresh process information from the API.
     *
     * @return object An object containing fresh process information (arch, platform, uptime, etc.).
     */
    public function fresh(): object
    {
        return (object) $this->client->get('process')->json();
    }
}
