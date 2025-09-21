<?php

namespace Native\Laravel\Contracts;

use Native\Laravel\DTOs\QueueConfig;

interface QueueWorker
{
    public function up(QueueConfig $config): void;

    public function down(string $alias): void;
}
