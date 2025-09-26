<?php

use Illuminate\Support\Arr;
use Native\Desktop\DataObjects\QueueConfig;

test('the factory method generates an array of config objects for several formats', function (array $config) {
    $configObject = QueueConfig::fromConfigArray($config);

    expect($configObject)->toBeArray();
    expect($configObject)->toHaveCount(count($config));

    foreach ($config as $alias => $worker) {
        if (is_string($worker)) {
            expect(
                Arr::first(
                    array_filter($configObject, fn (QueueConfig $config) => $config->alias === $worker))
            )->queuesToConsume->toBe(['default']
            );

            expect(Arr::first(array_filter($configObject,
                fn (QueueConfig $config) => $config->alias === $worker)))->memoryLimit->toBe(128);
            expect(Arr::first(array_filter($configObject,
                fn (QueueConfig $config) => $config->alias === $worker)))->timeout->toBe(60);
            expect(Arr::first(array_filter($configObject,
                fn (QueueConfig $config) => $config->alias === $worker)))->sleep->toBe(3);

            continue;
        }

        expect(
            Arr::first(
                array_filter($configObject, fn (QueueConfig $config) => $config->alias === $alias))
        )->queuesToConsume->toBe($worker['queues'] ?? ['default']
        );

        expect(Arr::first(array_filter($configObject,
            fn (QueueConfig $config) => $config->alias === $alias)))->memoryLimit->toBe($worker['memory_limit'] ?? 128);
        expect(Arr::first(array_filter($configObject,
            fn (QueueConfig $config) => $config->alias === $alias)))->timeout->toBe($worker['timeout'] ?? 60);
        expect(Arr::first(array_filter($configObject,
            fn (QueueConfig $config) => $config->alias === $alias)))->sleep->toBe($worker['sleep'] ?? 3);
    }
})->with([
    [
        [
            'queue_workers' => [
                'some_worker' => [
                    'queues' => ['default'],
                    'memory_limit' => 64,
                    'timeout' => 60,
                    'sleep' => 3,
                ],
            ],
        ],
    ],
    [
        [
            'queue_workers' => [
                'some_worker' => [],
                'another_worker' => [],
            ],
        ],
    ],
    [
        [
            'queue_workers' => [
                'some_worker' => [
                ],
                'another_worker' => [
                    'queues' => ['default', 'another'],
                ],
                'yet_another_worker' => [
                    'memory_limit' => 256,
                ],
                'one_more_worker' => [
                    'timeout' => 120,
                ],
            ],
        ],
    ],
]);
