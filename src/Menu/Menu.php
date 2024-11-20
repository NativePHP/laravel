<?php

namespace Native\Laravel\Menu;

use Illuminate\Support\Traits\Conditionable;
use Native\Laravel\Client\Client;
use Native\Laravel\Contracts\MenuItem;
use Native\Laravel\Enums\RolesEnum;
use Native\Laravel\Menu\Items\Checkbox;
use Native\Laravel\Menu\Items\Event;
use Native\Laravel\Menu\Items\Label;
use Native\Laravel\Menu\Items\Link;
use Native\Laravel\Menu\Items\Role;
use Native\Laravel\Menu\Items\Separator;

class Menu implements MenuItem
{
    use Conditionable;

    protected array $items = [];

    protected string $label = '';

    public function __construct(protected Client $client) {}

    public function register(): void
    {
        $items = $this->toArray()['submenu'];

        $this->client->post('menu', [
            'items' => $items,
        ]);
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function add(MenuItem $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function toArray(): array
    {
        $items = collect($this->items)
            ->map(fn (MenuItem $item) => $item->toArray())
            ->toArray();

        return [
            'label' => $this->label,
            'submenu' => $items,
        ];
    }
}
