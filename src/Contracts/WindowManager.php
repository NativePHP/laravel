<?php

namespace Native\Laravel\Contracts;

use Native\Laravel\Windows\Window;

interface WindowManager
{
    public function open(string $id = 'main');

    public function close($id = null);

    public function hide($id = null);

    public function current(): Window;

    /**
     * @return array<int, Window>
     */
    public function all(): array;

    public function get(string $id): Window;
}
