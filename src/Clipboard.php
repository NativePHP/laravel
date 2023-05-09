<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Clipboard
{
    public function __construct(protected Client $client)
    {
    }

    public function text($text = null): string
    {
        if (is_null($text)) {
            return $this->client->get('clipboard/text')->json('text');
        }

        $this->client->post('clipboard/text', [
            'text' => $text,
        ]);

        return $text;
    }
}
