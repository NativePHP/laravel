<?php

namespace Native\Laravel\Menu\Items;

use Native\Laravel\Enums\RolesEnum;

class Role extends MenuItem
{
    protected string $type = 'role';

    public function __construct(protected RolesEnum $role, protected ?string $label = '') {}

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'role' => $this->role->value,
        ]);
    }
}
