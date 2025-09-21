<?php

namespace Native\Laravel\Menu\Items;

class Label extends MenuItem
{
    public function __construct(
        protected ?string $label,
        protected ?string $accelerator = null
    ) {}
}
