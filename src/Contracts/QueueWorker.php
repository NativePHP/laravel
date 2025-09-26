<?php

namespace Native\Desktop\Contracts;

use Native\Desktop\DataObjects\QueueConfig;

interface QueueWorker
{
    public function up(QueueConfig $config): void;

    public function down(string $alias): void;
}
