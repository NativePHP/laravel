<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Clipboard
{
    /**
     * Constructor.
     *
     * @param Client $client The HTTP client instance.
     */
    public function __construct(protected Client $client)
    {
    }

    /**
     * Clear the clipboard.
     *
     * @return void
     */
    public function clear()
    {
        $this->client->delete('clipboard');
    }

    /**
     * Get or set text content in the clipboard.
     *
     * @param string|null $text The text to be set in the clipboard. If null, retrieve the current text content from the clipboard.
     * @return string The current or newly set text content in the clipboard.
     */
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

    /**
     * Get or set HTML content in the clipboard.
     *
     * @param string|null $html The HTML to be set in the clipboard. If null, retrieve the current HTML content from the clipboard.
     * @return string The current or newly set HTML content in the clipboard.
     */
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

    /**
     * Get or set image content in the clipboard.
     *
     * @param string|null $image The image data or file path to be set in the clipboard. If null, retrieve the current image content from the clipboard.
     * @return string The current or newly set image content in the clipboard.
     */
    public function image($image = null): string
    {
        if (is_null($image)) {
            return $this->client->get('clipboard/image')->json('image');
        }

        $dataUri = $image;

        if (is_string($image) && file_exists($image)) {
            $type = pathinfo($image, PATHINFO_EXTENSION);
            $data = file_get_contents($image);
            $dataUri = "data:image/{$type};base64," . base64_encode($data);
        }

        $this->client->post('clipboard/image', [
            'image' => $dataUri,
        ]);

        return $image;
    }
}
