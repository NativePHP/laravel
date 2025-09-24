<?php

namespace Native\Desktop\Menu\Items;

use Native\Desktop\Enums\RolesEnum;

class Role extends MenuItem
{
    protected string $type = 'role';

    public function __construct(
        protected RolesEnum $role,
        protected ?string $label = ''
    ) {}

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'role' => $this->role->value,
        ]);
    }
}
