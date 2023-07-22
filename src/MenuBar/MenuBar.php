<?php

namespace Native\Laravel\MenuBar;

use Native\Laravel\Client\Client;
use Native\Laravel\Concerns\HasDimensions;
use Native\Laravel\Concerns\HasUrl;
use Native\Laravel\Concerns\HasVibrancy;
use Native\Laravel\Menu\Menu;

class MenuBar
{
    use HasVibrancy;
    use HasDimensions;
    use HasUrl;

    /**
     * The icon to be displayed in the menu bar.
     *
     * @var string
     */
    protected string $icon = '';

    /**
     * The label to be displayed in the menu bar.
     *
     * @var string
     */
    protected string $label = '';

    /**
     * The context menu associated with the menu bar (if any).
     *
     * @var Menu|null
     */
    protected ?Menu $contextMenu = null;

    /**
     * Whether the menu bar should always be on top of other windows.
     *
     * @var bool
     */
    protected bool $alwaysOnTop = false;

    /**
     * Whether to show the application icon in the dock (macOS specific).
     *
     * @var bool
     */
    protected bool $showDockIcon = false;

    /**
     * The HTTP client instance.
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Create a new MenuBar instance.
     */
    public function __construct()
    {
        $this->url = url('/');
    }

    /**
     * Set the HTTP client instance.
     *
     * @param Client $client.
     * @return $this
     */
    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Set the icon to be displayed in the menu bar.
     *
     * @param string $icon The path to the icon file.
     * @return $this
     */
    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Set the URL to be opened when clicking on the menu bar.
     *
     * @param string $url The URL to be opened.
     * @return $this
     */
    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Set whether to show the application icon in the dock.
     *
     * @param bool $value Whether to show the dock icon (default: true).
     * @return $this
     */
    public function showDockIcon($value = true): self
    {
        $this->showDockIcon = $value;

        return $this;
    }

    /**
     * Set the label to be displayed in the menu bar.
     *
     * @param string $label The label text.
     * @return $this
     */
    public function label(string $label = ''): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set whether the menu bar should always be on top of other windows.
     *
     * @param bool $alwaysOnTop Whether the menu bar should always be on top (default: true).
     * @return $this
     */
    public function alwaysOnTop($alwaysOnTop = true): self
    {
        $this->alwaysOnTop = $alwaysOnTop;

        return $this;
    }

    /**
     * Set the context menu associated with the menu bar.
     *
     * @param Menu $menu The context menu instance.
     * @return $this
     */
    public function withContextMenu(Menu $menu): self
    {
        $this->contextMenu = $menu;

        return $this;
    }

    /**
     * Get the array representation of the MenuBar instance.
     *
     * @return array The array representation of the MenuBar instance.
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'icon' => $this->icon,
            'x' => $this->x,
            'y' => $this->y,
            'label' => $this->label,
            'width' => $this->width,
            'height' => $this->height,
            'vibrancy' => $this->vibrancy,
            'showDockIcon' => $this->showDockIcon,
            'transparency' => $this->transparent,
            'backgroundColor' => $this->backgroundColor,
            'contextMenu' => ! is_null($this->contextMenu) ? $this->contextMenu->toArray()['submenu'] : null,
            'alwaysOnTop' => $this->alwaysOnTop,
        ];
    }
}
