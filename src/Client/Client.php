<?php

namespace Native\Laravel\Client;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Client
{
    /**
     * The pending request instance.
     *
     * @var \Illuminate\Http\Client\PendingRequest
     */
    protected PendingRequest $client;

    /**
     * Create a new client instance.
     *
     * @return void
     */
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

    /**
     * Get the response from the http client.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function get(string $endpoint): Response
    {
        return $this->client->get($endpoint);
    }

    /**
     * Post the response from the http client.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function post(string $endpoint, array $data = []): Response
    {
        return $this->client->post($endpoint, $data);
    }

    /**
     * Delete the response from the http client.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function delete(string $endpoint, array $data = []): Response
    {
        return $this->client->delete($endpoint, $data);
    }
}
