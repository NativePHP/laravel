<?php

namespace Native\Laravel\Menu\Items;

class Radio extends MenuItem
{
    protected string $type = 'radio';

    public function __construct(
        string $label,
        protected bool $isChecked = false,
        protected ?string $accelerator = null
    ) {
        $this->label = $label;
    }
}
