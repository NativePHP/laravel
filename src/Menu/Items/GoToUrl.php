<?php

namespace Native\Laravel\Menu\Items;

class GoToUrl extends MenuItem
{
    public function __construct(
        protected string $url,
        protected ?string $label = null,
        protected ?string $accelerator = null
    ) {}

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'type' => 'goto',
            'url' => $this->url,
            'label' => $this->label,
        ]);
    }
}
