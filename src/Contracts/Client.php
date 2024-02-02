<?php

namespace Native\Laravel\Contracts;

use Illuminate\Http\Client\Response;

interface Client
{
    public function get(string $resource): Response;
    public function post(string $resource, array $data = []): Response;
    public function delete(string $resource, array $data = []): Response;
}
