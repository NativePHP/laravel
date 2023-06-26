<?php

namespace Native\Laravel\Windows;

use Illuminate\Support\Facades\URL;
use Native\Laravel\Client\Client;
use Native\Laravel\Concerns\HasDimensions;
use Native\Laravel\Concerns\HasUrl;
use Native\Laravel\Concerns\HasVibrancy;

class Window
{
    use HasVibrancy;
    use HasDimensions;
    use HasUrl;

    protected $manageState = false;

    protected bool $alwaysOnTop = false;

    protected bool $resizable = true;

    protected bool $focusable = true;

    protected bool $hasShadow = true;

    protected bool $frame = true;

    protected string $titleBarStyle = 'default';

    protected string $title = '';

    protected string $id = 'main';

    protected Client $client;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->title = config('app.name');
        $this->url = url('/');
    }

    public function id(string $id = 'main'): self
    {
        $this->id = $id;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

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

    public function manageWindowState(): self
    {
        $this->manageState = true;

        return $this;
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

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function alwaysOnTop($alwaysOnTop = true): self
    {
        $this->alwaysOnTop = $alwaysOnTop;

        return $this;
    }

    public function resizable($resizable = true): static
    {
        $this->resizable = $resizable;

        return $this;
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

    public function toArray()
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'x' => $this->x,
            'y' => $this->y,
            'manageState' => $this->manageState,
            'width' => $this->width,
            'height' => $this->height,
            'minWidth' => $this->minWidth,
            'minHeight' => $this->minHeight,
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
        ];
    }
}
