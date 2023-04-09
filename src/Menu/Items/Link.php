<?php

namespace Native\Laravel\Menu\Items;

use Native\Laravel\Contracts\MenuItem;

class Link implements MenuItem
{
    public function __construct(protected string $url, protected string $label, protected ?string $hotkey = null)
    {

    }

    public function toArray(): array
    {
        return [
            'type' => 'link',
            'accelerator' => $this->hotkey,
            'url' => $this->url,
            'label' => $this->label,
        ];
    }
}
