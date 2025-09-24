<?php

namespace Native\Desktop\Contracts;

use Native\Desktop\DTOs\QueueConfig;

interface QueueWorker
{
    public function up(QueueConfig $config): void;

    public function down(string $alias): void;
}
