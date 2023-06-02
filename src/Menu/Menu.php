<?php

namespace Native\Laravel\Menu;

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
    protected array $items = [];

    protected string $prepend = '';

    public function __construct(protected Client $client)
    {
    }

    public static function new(): static
    {
        return new static(new Client());
    }

    public function register(): void
    {
        $items = $this->toArray()['submenu'];

        $this->client->post('menu', [
            'items' => $items,
        ]);
    }

    public function prepend(string $prepend): self
    {
        $this->prepend = $prepend;

        return $this;
    }

    public function submenu(string $header, Menu $submenu): static
    {
        return $this->add($submenu->prepend($header));
    }

    public function separator(): static
    {
        return $this->add(new Separator());
    }

    public function quit(): static
    {
        return $this->add(new Role(RolesEnum::QUIT));
    }

    public function label(string $label): self
    {
        return $this->add(new Label($label));
    }

    public function checkbox(string $label, bool $checked = false): self
    {
        return $this->add(new Checkbox($label, $checked));
    }

    public function event(string $event, string $text): self
    {
        return $this->add(new Event($event, $text));
    }

    public function link(string $url, string $text, ?string $hotkey = null): self
    {
        return $this->add(new Link($url, $text, $hotkey));
    }

    public function appMenu(): static
    {
        return $this->add(new Role(RolesEnum::APP_MENU));
    }

    public function toggleFullscreen(): static
    {
        return $this->add(new Role(RolesEnum::TOGGLE_FULL_SCREEN));
    }

    public function add(MenuItem $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function toArray(): array
    {
        $items = collect($this->items)->map(fn (MenuItem $item) => $item->toArray())->toArray();
        $label = $this->prepend;

        return [
            'label' => $label,
            'submenu' => $items,
        ];
    }
}
