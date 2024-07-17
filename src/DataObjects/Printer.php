<?php

namespace Native\Laravel\DataObjects;

class Printer
{
    public function __construct(
        public string $name,
        public string $displayName,
        public string $description,
        public int $status,
        public bool $isDefault,
        public array $options
    ) {}
}
