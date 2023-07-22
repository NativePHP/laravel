<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Screen
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
     * Get the current cursor position on the screen.
     *
     * @return object An object containing the cursor position properties.
     */
    public function cursorPosition(): object
    {
        return (object) $this->client->get('screen/cursor-position')->json();
    }

    /**
     * Get information about connected displays.
     *
     * @return array An array of display information.
     */
    public function displays(): array
    {
        return $this->client->get('screen/displays')->json('displays');
    }
}
