<?php

namespace Native\Desktop\Contracts;

interface GlobalShortcut
{
    public function key(string $key): self;

    public function event(string $event): self;

    public function register(): void;

    public function unregister(): void;
}
