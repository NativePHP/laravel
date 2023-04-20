<?php

namespace Native\Laravel\Menu\Items;

use Native\Laravel\Contracts\MenuItem;

class Quit implements MenuItem
{
    public function __construct(protected string $label)
    {

    }

    public function toArray(): array
    {
        return [
            'type' => 'quit',
            'label' => $this->label,
        ];
    }
}
