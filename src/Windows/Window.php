<?php

namespace Native\Laravel\Windows;

use Native\Laravel\Client\Client;
use Native\Laravel\Concerns\HasDimensions;
use Native\Laravel\Concerns\HasUrl;
use Native\Laravel\Concerns\HasVibrancy;

class Window
{
    use HasVibrancy;
    use HasDimensions;
    use HasUrl;

    /**
     * Whether the window is remember state.
     *
     * @var bool
     */
    protected $rememberState = false;

    /**
     * Whether the window is always on top.
     *
     * @var bool
     */
    protected bool $alwaysOnTop = false;

    /**
     * Whether the window is show dev tools.
     *
     * @var bool
     */
    protected bool $showDevTools = false;

    /**
     * Whether the window is resizable.
     *
     * @var bool
     */
    protected bool $resizable = true;

    /**
     * Whether the window is movable.
     *
     * @var bool
     */
    protected bool $movable = true;

    /**
     * Whether the window is minimizable.
     *
     * @var bool
     */
    protected bool $minimizable = true;

    /**
     * Whether the window is maximizable.
     *
     * @var bool
     */
    protected bool $maximizable = true;

    /**
     * Whether the window is closable.
     *
     * @var bool
     */
    protected bool $closable = true;

    /**
     * Whether the window is focusable.
     *
     * @var bool
     */
    protected bool $focusable = true;

    /**
     * Whether the window has a shadow.
     *
     * @var bool
     */
    protected bool $hasShadow = true;

    /**
     * Whether the window is frameless.
     *
     * @var bool
     */
    protected bool $frame = true;

    /**
     * The style of the title bar.
     *
     * @var string
     */
    protected string $titleBarStyle = 'default';

    /**
     * The title of the window.
     *
     * @var string
     */
    protected string $title = '';

    /**
     * The unique identifier for the window.
     *
     * @var string
     */
    protected string $id = 'main';

    /**
     * The HTTP client instance.
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Constructor.
     *
     * @param string $id The unique identifier for the window.
     */
    public function __construct(string $id)
    {
        $this->id = $id;
        $this->title = config('app.name');
        $this->url = url('/');
        $this->showDevTools = config('app.debug');
    }

    /**
     * Set the unique identifier for the window.
     *
     * @param string $id The unique identifier for the window.
     * @return self
     */
    public function id(string $id = 'main'): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set the title of the window.
     *
     * @param string $title The title of the window.
     * @return self
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the style of the title bar.
     *
     * @param string $style The style of the title bar.
     * @return self
     */
    public function titleBarStyle($style): self
    {
        $this->titleBarStyle = $style;

        return $this;
    }

    /**
     * Set the title bar style to hidden.
     *
     * @return self
     */
    public function titleBarHidden(): self
    {
        return $this->titleBarStyle('hidden');
    }

    /**
     * Set the title bar style to hiddenInset.
     *
     * @return self
     */
    public function titleBarHiddenInset(): self
    {
        return $this->titleBarStyle('hiddenInset');
    }

    /**
     * Enable remembering window state.
     *
     * @return self
     */
    public function rememberState(): self
    {
        $this->rememberState = true;

        return $this;
    }

    /**
     * Set the window to be frameless.
     *
     * @return self
     */
    public function frameless(): self
    {
        $this->frame = false;

        return $this;
    }

    /**
     * Set the window focusability.
     *
     * @param bool $value Whether the window is focusable.
     * @return self
     */
    public function focusable($value = true): self
    {
        $this->focusable = $value;

        return $this;
    }

    /**
     * Set whether the window has a shadow.
     *
     * @param bool $value Whether the window has a shadow.
     * @return self
     */
    public function hasShadow($value = true): self
    {
        $this->hasShadow = $value;

        return $this;
    }

    /**
     * Set the title bar style to customButtonsOnHover.
     *
     * @return self
     */
    public function titleBarButtonsOnHover(): self
    {
        return $this->titleBarStyle('customButtonsOnHover');
    }

    /**
     * Set the HTTP client to make API requests.
     *
     * @param Client $client The HTTP client instance to be set.
     * @return self
     */
    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Set the window to always be on top.
     *
     * @param bool $alwaysOnTop Whether the window is always on top.
     * @return self
     */
    public function alwaysOnTop($alwaysOnTop = true): self
    {
        $this->alwaysOnTop = $alwaysOnTop;

        return $this;
    }

    /**
     * Set whether to show developer tools for the window.
     *
     * @param bool $showDevTools Whether to show developer tools.
     * @return self
     */
    public function showDevTools($showDevTools = true): self
    {
        $this->showDevTools = $showDevTools;

        return $this;
    }

    /**
     * Set whether the window is resizable.
     *
     * @param bool $resizable Whether the window is resizable.
     * @return static
     */
    public function resizable($resizable = true): static
    {
        $this->resizable = $resizable;

        return $this;
    }

    /**
     * Set whether the window is movable.
     *
     * @param bool $movable Whether the window is movable.
     * @return static
     */
    public function movable($movable = true): static
    {
        $this->movable = $movable;

        return $this;
    }

    /**
     * Set whether the window is minimizable.
     *
     * @param bool $minimizable Whether the window is minimizable.
     * @return static
     */
    public function minimizable($minimizable = true): static
    {
        $this->minimizable = $minimizable;

        return $this;
    }

    /**
     * Set whether the window is maximizable.
     *
     * @param bool $maximizable Whether the window is maximizable.
     * @return static
     */
    public function maximizable($maximizable = true): static
    {
        $this->maximizable = $maximizable;

        return $this;
    }

    /**
     * Set whether the window is closable.
     *
     * @param bool $closable Whether the window is closable.
     * @return static
     */
    public function closable($closable = true): static
    {
        $this->closable = $closable;

        return $this;
    }

    /**
     * Set the window to be invisible frameless.
     *
     * @return self
     */
    public function invisibleFrameless(): self
    {
        return $this
            ->frameless()
            ->transparent()
            ->focusable(false)
            ->hasShadow(false);
    }

    /**
     * Convert the window properties to an array.
     *
     * @return array
     */
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
        ];
    }
}
