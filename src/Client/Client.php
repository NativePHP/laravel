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
            ->baseUrl(config('native-php.api_url', ''))
            ->timeout(60 * 60)
            ->withHeaders([
                'X-Native-PHP-Secret' => config('native-php.secret'),
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
