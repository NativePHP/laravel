<?php

namespace Native\Laravel\Menu\Items;

class Label extends MenuItem
{
    public function __construct(string $label)
    {
        $this->label = $label;
    }
}
