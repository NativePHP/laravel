<?php

namespace Native\Laravel;

use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Native\Laravel\Client\Client;
use Native\Laravel\Facades\Window;

class Dialog
{
    /**
     * Trait for conditionable methods.
     */
    use Conditionable;

    /**
     * Trait for macroable methods.
     */
    use Macroable;

    /**
     * The title of the dialog.
     *
     * @var string
     */
    protected $title;

    /**
     * The default path for the dialog.
     *
     * @var string
     */
    protected $defaultPath;

    /**
     * The label for the dialog button.
     *
     * @var string
     */
    protected $buttonLabel = 'Select';

    /**
     * The properties of the dialog.
     *
     * @var array
     */
    protected $properties = [
        'openFile',
    ];

    /**
     * The filters for the dialog.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * The reference to the window where the dialog will be shown as a sheet.
     *
     * @var string|null
     */
    protected $windowReference;

    /**
     * Constructor.
     *
     * @param Client $client The HTTP client instance.
     */
    public function __construct(protected Client $client)
    {
    }

    /**
     * Create a new instance of the Dialog class.
     *
     * @return Dialog A new instance of the Dialog class.
     */
    public static function new()
    {
        return new static(new Client());
    }

    /**
     * Set the title of the dialog.
     *
     * @param string $title The title of the dialog.
     * @return self Returns the current instance of the Dialog class.
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function defaultPath(string $defaultPath): self
    {
        $this->defaultPath = $defaultPath;

        return $this;
    }

    /**
     * Show the dialog as a sheet in the specified window.
     *
     * @param string|null $windowId The ID of the window where the dialog should be shown as a sheet. If null, the current window is used.
     * @return self Returns the current instance of the Dialog class.
     */
    public function asSheet(string $windowId = null): self
    {
        if (is_null($windowId)) {
            $this->windowReference = Window::current()->id;
        } else {
            $this->windowReference = $windowId;
        }

        return $this;
    }

    /**
     * Open the dialog and return the selected file(s) (single selection) or an array of selected files (multiple selection).
     *
     * @return string|array|null The selected file(s) if single selection, or an array of selected files if multiple selection. Returns null if nothing is selected.
     */
    public function open()
    {
        $result = $this->client->post('dialog/open', [
            'title' => $this->title,
            'windowReference' => $this->windowReference,
            'defaultPath' => $this->defaultPath,
            'filters' => $this->filters,
            'buttonLabel' => $this->buttonLabel,
            'properties' => array_unique($this->properties),
        ])->json('result');

        if (! in_array('multiSelections', $this->properties)) {
            return $result[0] ?? null;
        }

        return $result;
    }

    /**
     * Save the dialog.
     *
     * @return string|null
     */
    public function save()
    {
        return $this->client->post('dialog/save', [
            'title' => $this->title,
            'windowReference' => $this->windowReference,
            'defaultPath' => $this->defaultPath,
            'filters' => $this->filters,
            'buttonLabel' => $this->buttonLabel,
            'properties' => array_unique($this->properties),
        ])->json('result');
    }
}
