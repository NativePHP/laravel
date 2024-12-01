<?php

namespace Native\Laravel\Menu\Items;

use Native\Laravel\Contracts\MenuItem as MenuItemContract;
use Native\Laravel\Facades\Menu as MenuFacade;
use Native\Laravel\Menu\Menu;

abstract class MenuItem implements MenuItemContract
{
    protected string $type = 'normal';

    protected ?string $id = null;

    protected ?string $label = null;

    protected ?string $sublabel = null;

    protected ?string $accelerator = null;

    protected ?string $icon = null;

    protected ?string $toolTip = null;

    protected ?Menu $submenu = null;

    protected bool $isEnabled = true;

    protected bool $isVisible = true;

    protected bool $isChecked = false;

    protected ?string $event = null;

    public function enabled(): self
    {
        $this->isEnabled = true;

        return $this;
    }

    public function disabled(): self
    {
        $this->isEnabled = false;

        return $this;
    }

    public function id(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function sublabel(string $sublabel): self
    {
        $this->sublabel = $sublabel;

        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function visible($visible = true): self
    {
        $this->isVisible = $visible;

        return $this;
    }

    public function accelerator(string $accelerator): self
    {
        $this->accelerator = $accelerator;

        return $this;
    }

    public function hotkey(string $hotkey): self
    {
        return $this->accelerator($hotkey);
    }

    public function checked($checked = true): self
    {
        $this->isChecked = $checked;

        return $this;
    }

    public function tooltip(string $toolTip): self
    {
        $this->toolTip = $toolTip;

        return $this;
    }

    public function submenu(MenuItemContract ...$items): self
    {
        $this->submenu = MenuFacade::make(...$items);

        return $this;
    }

    public function event(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'id' => $this->id,
            'label' => $this->label,
            'event' => $this->event,
            'sublabel' => $this->sublabel,
            'toolTip' => $this->toolTip,
            'enabled' => $this->isEnabled,
            'visible' => $this->isVisible,
            'checked' => $this->isChecked,
            'accelerator' => $this->accelerator,
            'icon' => $this->icon,
            'submenu' => $this->submenu?->toArray(),
        ], fn ($value) => $value !== null);
    }
}
