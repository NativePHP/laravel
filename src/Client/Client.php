<?php

namespace Native\Laravel\Client;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Client
{
    protected PendingRequest $client;

    public function __construct()
    {
        $this->client = Http::asJson()
            ->baseUrl(config('nativephp-internal.api_url', ''))
            ->timeout(60 * 60)
            ->withHeaders([
                'X-NativePHP-Secret' => config('nativephp-internal.secret'),
            ])
            ->asJson();
    }

    public function get(string $endpoint): Response
    {
        return $this->client->get($endpoint);
    }

    public function post(string $endpoint, array $data = []): Response
    {
        return $this->client->post($endpoint, $data);
    }

    public function delete(string $endpoint, array $data = []): Response
    {
        return $this->client->delete($endpoint, $data);
    }
}
