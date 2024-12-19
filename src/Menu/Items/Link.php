<?php

namespace Native\Laravel\Menu\Items;

class Link extends MenuItem
{
    protected string $type = 'link';

    protected bool $openInBrowser = false;

    public function __construct(
        protected string $url,
        protected ?string $label,
        protected ?string $accelerator = null
    ) {}

    public function openInBrowser(bool $openInBrowser = true): self
    {
        $this->openInBrowser = $openInBrowser;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'url' => $this->url,
            'openInBrowser' => $this->openInBrowser,
        ]);
    }
}
