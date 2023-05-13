<?php

namespace Native\Laravel;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Native\Laravel\Client\Client;

class Window
{
    protected string $url = '';

    protected $x;

    protected $y;

    protected int $width = 400;

    protected int $height = 400;

    protected bool $alwaysOnTop = false;

    protected bool $resizable = true;

    protected bool $transparent = false;

    protected bool $focusable = true;

    protected bool $hasShadow = true;

    protected bool $frame = true;

    protected string $titleBarStyle = 'default';

    protected string $vibrancy = 'appearance-based';

    protected string $backgroundColor = '#FFFFFF';

    protected string $title = '';

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

    public function close($id = null): void
    {
        $this->client->post('window/close', [
            'id' => $id ?? $this->detectId(),
        ]);
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

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

    public function titleBarStyle($style): self
    {
        $this->titleBarStyle = $style;

        return $this;
    }

    public function titleBarHidden(): self
    {
        return $this->titleBarStyle('hidden');
    }

    public function titleBarHiddenInset(): self
    {
        return $this->titleBarStyle('hiddenInset');
    }

    public function frameless(): self
    {
        $this->frame = false;

        return $this;
    }

    public function focusable($value = true): self
    {
        $this->focusable = $value;

        return $this;
    }

    public function hasShadow($value = true): self
    {
        $this->hasShadow = $value;

        return $this;
    }

    public function titleBarButtonsOnHover(): self
    {
        return $this->titleBarStyle('customButtonsOnHover');
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

    public function resizable($resizable = true): static
    {
        $this->resizable = $resizable;

        return $this;
    }

    public function position($x, $y): self
    {
        $this->x = $x;
        $this->y = $y;

        return $this;
    }

    public function open(): void
    {
        $this->client->post('window/open', [
            'id' => $this->id,
            'url' => $this->url,
            'x' => $this->x,
            'y' => $this->y,
            'width' => $this->width,
            'height' => $this->height,
            'focusable' => $this->focusable,
            'hasShadow' => $this->hasShadow,
            'frame' => $this->frame,
            'titleBarStyle' => $this->titleBarStyle,
            'vibrancy' => $this->vibrancy,
            'transparency' => $this->transparent,
            'backgroundColor' => $this->backgroundColor,
            'alwaysOnTop' => $this->alwaysOnTop,
            'resizable' => $this->resizable,
            'title' => $this->title,
        ]);
    }

    public function current()
    {
        return (object) $this->client->get('window/current')->json();
    }

    public function invisibleFrameless(): self
    {
        return $this
            ->frameless()
            ->transparent()
            ->focusable(false)
            ->hasShadow(false);
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

    public function alwaysOnTop($alwaysOnTop): void
    {
        $this->client->post('window/always-on-top', [
            'id' => $this->detectId(),
            'alwaysOnTop' => $alwaysOnTop,
        ]);
    }
}
