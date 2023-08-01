<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;
use Native\Laravel\DataObjects\Printer;

class System
{
    public function __construct(protected Client $client)
    {
    }

    public function canPromptTouchID(): bool
    {
        return $this->client->get('system/can-prompt-touch-id')->json('result');
    }

    public function promptTouchID(string $reason): bool
    {
        return $this->client->post('system/prompt-touch-id', [
            'reason' => $reason,
        ])->successful();
    }

    /**
     * @return array<\Native\Laravel\DataObjects\Printer>
     */
    public function printers(): array
    {
        $printers = $this->client->get('system/printers')->json('printers');

        return collect($printers)->map(function ($printer) {
            return new Printer(
                data_get($printer, 'name'),
                data_get($printer, 'displayName'),
                data_get($printer, 'description'),
                data_get($printer, 'status'),
                data_get($printer, 'isDefault'),
                data_get($printer, 'options'),
            );
        })->toArray();
    }

    public function print(string $html, Printer $printer = null): void
    {
        $this->client->post('system/print', [
            'html' => $html,
            'printer' => $printer->name ?? '',
        ]);
    }
}
