<?php

namespace Native\Laravel\Menu\Items;

use Native\Laravel\Contracts\MenuItem;

class Event implements MenuItem
{
    public function __construct(protected string $event, protected string $label)
    {

    }

    public function toArray(): array
    {
        return [
            'type' => 'event',
            'event' => $this->event,
            'label' => $this->label,
        ];
    }
}
