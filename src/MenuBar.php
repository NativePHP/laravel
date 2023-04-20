<?php

namespace Native\Laravel;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Native\Laravel\Client\Client;

class MenuBar
{
    protected string $url = '';

    protected string $icon = '';

    protected int $width = 400;

    protected int $height = 400;

    protected $contextWindow;

    protected bool $alwaysOnTop = false;

    protected bool $transparent = false;

    protected bool $showDockIcon = false;

    protected string $vibrancy = 'appearance-based';

    protected string $backgroundColor = '#FFFFFF';

    protected string $id;

    public function __construct(protected Client $client)
    {
        $this->id = Str::uuid();
        $this->url = url('/');
    }

    public static function new(): static
    {
        return new static(new Client());
    }

    public function setLabel(string $label)
    {
        $this->client->post('menubar/label', [
            'label' => $label,
        ]);
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function close($id = null): void
    {
        $this->client->post('menubar/close', [
            'id' => $id ?? $this->detectId(),
        ]);
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function showDockIcon($value = true): self
    {
        $this->showDockIcon = $value;

        return $this;
    }

    public function transparent($value = true): self
    {
        $this->transparent = $value;
        if ($value === true) {
            $this->backgroundColor = '#00000000';
        }

        return $this;
    }

    public function vibrancy(string $vibrancy): self
    {
        $this->vibrancy = $vibrancy;

        return $this;
    }

    public function lightVibrancy(): self
    {
        return $this->vibrancy('light');
    }

    public function blendBackgroundBehindWindow(): self
    {
        $this->transparent();

        return $this->vibrancy('under-window');
    }

    public function darkVibrancy(): self
    {
        return $this->vibrancy('dark');
    }

    public function width($width): self
    {
        $this->width = $width;

        return $this;
    }

    public function height($height): self
    {
        $this->height = $height;

        return $this;
    }

    public function create(): void
    {
        $this->client->post('menubar', [
            'id' => $this->id,
            'url' => $this->url,
            'icon' => $this->icon,
            'width' => $this->width,
            'height' => $this->height,
            'vibrancy' => $this->vibrancy,
            'showDockIcon' => $this->showDockIcon,
            'transparency' => $this->transparent,
            'backgroundColor' => $this->backgroundColor,
            'alwaysOnTop' => $this->alwaysOnTop,
        ]);
    }

    public function detectId(): ?string
    {
        $previousUrl = request()->headers->get('Referer');
        $currentUrl = URL::current();

        // Return the _windowId query parameter from either the previous or current URL.
        $parsedUrl = parse_url($previousUrl ?? $currentUrl);
        parse_str($parsedUrl['query'] ?? '', $query);

        return $query['_windowId'] ?? null;
    }

    public function resize($width, $height): void
    {
        $this->client->post('window/resize', [
            'id' => $this->detectId(),
            'width' => $width,
            'height' => $height,
        ]);
    }

    public function alwaysOnTop($alwaysOnTop = true): self
    {
        $this->alwaysOnTop = $alwaysOnTop;

        return $this;
    }

    public function withContextMenu($menu): self
    {
        $this->contextMenu = $menu;

        return $this;
    }
}
