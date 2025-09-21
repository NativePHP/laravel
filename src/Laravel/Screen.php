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

    public function primary(): array
    {
        return $this->client->get('screen/primary-display')->json('primaryDisplay');
    }

    public function active(): array
    {
        return $this->client->get('screen/active')->json();
    }

    /**
     *  Returns the center of the screen where the mouse pointer is placed.
     *
     * @return array<string,int>
     */
    public function getCenterOfActiveScreen(): array
    {
        /* Navigate every screen and check for cursor position against the bounds of the screen. */
        $activeScreen = $this->active();

        $bounds = $activeScreen['bounds'];

        return [
            'x' => $bounds['x'] + $bounds['width'] / 2,
            'y' => $bounds['y'] + $bounds['height'] / 2,
        ];
    }
}
