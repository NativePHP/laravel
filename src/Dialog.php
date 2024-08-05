<?php

namespace Native\Laravel;

use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Native\Laravel\Client\Client;
use Native\Laravel\Facades\Window;

class Dialog
{
    use Conditionable;
    use Macroable;

    protected $title;

    protected $defaultPath;

    protected $buttonLabel = 'Select';

    protected $properties = [
        'openFile',
    ];

    protected $filters = [];

    protected $windowReference;

    public function __construct(protected Client $client) {}

    public static function new()
    {
        return new static(new Client);
    }

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

    public function button(string $buttonLabel): self
    {
        $this->buttonLabel = $buttonLabel;

        return $this;
    }

    public function multiple()
    {
        $this->properties[] = 'multiSelections';

        return $this;
    }

    public function withHiddenFiles()
    {
        $this->properties[] = 'showHiddenFiles';

        return $this;
    }

    public function files()
    {
        $this->properties = array_diff($this->properties, ['openDirectory']);
        $this->properties = ['openFile'];

        return $this;
    }

    public function folders()
    {
        $this->properties = array_diff($this->properties, ['openFile']);
        $this->properties[] = 'openDirectory';

        return $this;
    }

    public function dontResolveSymlinks(): self
    {
        $this->properties[] = 'noResolveAliases';

        return $this;
    }

    public function filter(string $name, array $extensions): self
    {
        $this->filters[] = [
            'name' => $name,
            'extensions' => $extensions,
        ];

        return $this;
    }

    public function properties(array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function asSheet(?string $windowId = null): self
    {
        if (is_null($windowId)) {
            $this->windowReference = Window::current()->id;
        } else {
            $this->windowReference = $windowId;
        }

        return $this;
    }

    public function open()
    {
        $result = $this->client->post('dialog/open', $this->dialogData())->json('result');

        if (! in_array('multiSelections', $this->properties)) {
            return $result[0] ?? null;
        }

        return $result;
    }

    public function save()
    {
        return $this->client->post('dialog/save', $this->dialogData())->json('result');
    }

    public function dialogData(): array
    {
        return [
            'title' => $this->title,
            'windowReference' => $this->windowReference,
            'defaultPath' => $this->defaultPath,
            'filters' => $this->filters,
            'buttonLabel' => $this->buttonLabel,
            'properties' => array_unique($this->properties),
        ];
    }
}
