<?php

namespace Native\Laravel\Menu\Items;

class Link extends MenuItem
{
    protected string $type = 'link';

    public function __construct(protected string $url, protected ?string $label, protected ?string $accelerator = null) {}

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'url' => $this->url,
        ]);
    }
}
