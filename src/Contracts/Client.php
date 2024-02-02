<?php

namespace Native\Laravel\Contracts;

interface Client
{
    public function get(string $resource);
    public function post(string $resource, array $data = []);
    public function delete(string $resource, array $data = []);
}
