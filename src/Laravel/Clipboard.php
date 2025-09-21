<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Clipboard
{
    public function __construct(protected Client $client) {}

    public function clear()
    {
        $this->client->delete('clipboard');
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

    public function html($html = null): string
    {
        if (is_null($html)) {
            return $this->client->get('clipboard/html')->json('html');
        }

        $this->client->post('clipboard/html', [
            'html' => $html,
        ]);

        return $html;
    }

    public function image($image = null): ?string
    {
        if (is_null($image)) {
            return $this->client->get('clipboard/image')->json('image');
        }

        $dataUri = $image;

        if (is_string($image) && file_exists($image)) {
            $type = pathinfo($image, PATHINFO_EXTENSION);
            $data = file_get_contents($image);
            $dataUri = "data:image/{$type};base64,".base64_encode($data);
        }

        $this->client->post('clipboard/image', [
            'image' => $dataUri,
        ]);

        return $image;
    }
}
