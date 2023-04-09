<?php

namespace Native\Laravel\Menu\Items;

use Native\Laravel\Contracts\MenuItem;
use Native\Laravel\Enums\RolesEnum;

class Role implements MenuItem
{
    public function __construct(protected RolesEnum $role)
    {

    }

    public function toArray(): array
    {
        return [
            'type' => 'role',
            'role' => $this->role->value,
        ];
    }
}
