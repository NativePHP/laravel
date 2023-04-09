<?php

namespace Native\Laravel\Menu\Items;

use Native\Laravel\Contracts\MenuItem;

class Separator implements MenuItem
{
    public function toArray(): array
    {
        return [
            'type' => 'separator',
        ];
    }
}
