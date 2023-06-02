<?php

namespace Native\Laravel\Menu\Items;

use Native\Laravel\Contracts\MenuItem as MenuItemContract;

abstract class MenuItem implements MenuItemContract
{
    protected string $type = 'normal';

    protected ?string $label = null;

    protected ?string $sublabel = null;

    protected ?string $accelerator = null;

    protected ?string $icon = null;

    protected ?string $toolTip = null;

    protected bool $isEnabled = true;

    protected bool $isVisible = true;

    protected bool $isChecked = false;

    public function enabled($enabled = true): self
    {
        $this->isEnabled = $enabled;

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

    public function checked($checked = true): self
    {
        $this->isChecked = $checked;

        return $this;
    }

    public function toolTip(string $toolTip): self
    {
        $this->toolTip = $toolTip;

        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'label' => $this->label,
            'sublabel' => $this->sublabel,
            'toolTip' => $this->toolTip,
            'enabled' => $this->isEnabled,
            'visible' => $this->isVisible,
            'checked' => $this->isChecked,
            'accelerator' => $this->accelerator,
            'icon' => $this->icon,
        ], fn ($value) => $value !== null);
    }
}
