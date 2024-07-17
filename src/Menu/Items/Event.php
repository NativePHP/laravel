<?php

namespace Native\Laravel\Menu\Items;

class Event extends MenuItem
{
    public function __construct(protected string $event, protected ?string $label, protected ?string $accelerator = null) {}

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'type' => 'event',
            'event' => $this->event,
            'label' => $this->label,
        ]);
    }
}
