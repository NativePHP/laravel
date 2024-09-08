<?php

namespace Native\Laravel\Windows;

use Native\Laravel\Client\Client;
use Native\Laravel\Concerns\HasDimensions;
use Native\Laravel\Concerns\HasUrl;
use Native\Laravel\Concerns\HasVibrancy;
use Native\Laravel\Facades\Window as WindowFacade;

class Window
{
    use HasDimensions;
    use HasUrl;
    use HasVibrancy;

    protected bool $autoHideMenuBar = false;

    protected bool $fullscreen = false;

    protected bool $fullscreenable = true;

    protected bool $kiosk = false;

    protected $rememberState = false;

    protected bool $alwaysOnTop = false;

    protected bool $showDevTools = false;

    protected bool $resizable = true;

    protected bool $movable = true;

    protected bool $minimizable = true;

    protected bool $maximizable = true;

    protected bool $closable = true;

    protected bool $focusable = true;

    protected bool $hasShadow = true;

    protected bool $frame = true;

    protected string $titleBarStyle = 'default';

    protected array $trafficLightPosition = [];

    protected string $title = '';

    protected string $id = 'main';

    protected Client $client;

    protected array $afterOpenCallbacks = [];

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->title = config('app.name');
        $this->url = url('/');
        $this->showDevTools = config('app.debug');
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

    public function trafficLightPosition(int $x, int $y): self
    {
        $this->trafficLightPosition = ['x' => $x, 'y' => $y];

        return $this;
    }

    public function rememberState(): self
    {
        $this->rememberState = true;

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

    public function showDevTools($showDevTools = true): self
    {
        $this->showDevTools = $showDevTools;

        return $this;
    }

    public function resizable($resizable = true): static
    {
        $this->resizable = $resizable;

        return $this;
    }

    public function movable($movable = true): static
    {
        $this->movable = $movable;

        return $this;
    }

    public function minimizable($minimizable = true): static
    {
        $this->minimizable = $minimizable;

        return $this;
    }

    public function maximizable($maximizable = true): static
    {
        $this->maximizable = $maximizable;

        return $this;
    }

    public function minimized(): static
    {
        return $this->afterOpen(fn () => WindowFacade::minimize($this->id));
    }

    public function maximized(): static
    {
        return $this->afterOpen(fn () => WindowFacade::maximize($this->id));
    }

    public function closable($closable = true): static
    {
        $this->closable = $closable;

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

    public function hideMenu($autoHideMenuBar = true): static
    {
        $this->autoHideMenuBar = $autoHideMenuBar;

        return $this;
    }

    public function fullscreen($fullscreen = true): static
    {
        $this->fullscreen = $fullscreen;

        return $this;
    }

    public function fullscreenable($fullscreenable = true): static
    {
        $this->fullscreenable = $fullscreenable;

        return $this;
    }

    public function kiosk($kiosk = false): static
    {
        $this->kiosk = $kiosk;

        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'x' => $this->x,
            'y' => $this->y,
            'rememberState' => $this->rememberState,
            'width' => $this->width,
            'height' => $this->height,
            'minWidth' => $this->minWidth,
            'minHeight' => $this->minHeight,
            'maxWidth' => $this->maxWidth,
            'maxHeight' => $this->maxHeight,
            'focusable' => $this->focusable,
            'hasShadow' => $this->hasShadow,
            'frame' => $this->frame,
            'titleBarStyle' => $this->titleBarStyle,
            'trafficLightPosition' => $this->trafficLightPosition,
            'showDevTools' => $this->showDevTools,
            'vibrancy' => $this->vibrancy,
            'transparency' => $this->transparent,
            'backgroundColor' => $this->backgroundColor,
            'alwaysOnTop' => $this->alwaysOnTop,
            'resizable' => $this->resizable,
            'movable' => $this->movable,
            'minimizable' => $this->minimizable,
            'maximizable' => $this->maximizable,
            'closable' => $this->closable,
            'title' => $this->title,
            'fullscreen' => $this->fullscreen,
            'fullscreenable' => $this->fullscreenable,
            'kiosk' => $this->kiosk,
            'autoHideMenuBar' => $this->autoHideMenuBar,
            'transparent' => $this->transparent,
        ];
    }

    public function afterOpen(callable $cb): static
    {
        $this->afterOpenCallbacks[] = $cb;

        return $this;
    }
}
