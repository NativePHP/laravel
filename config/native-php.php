<?php

return [
    'version' => env('NATIVE_PHP_VERSION', '1.0.0'),

    'secret' => env('NATIVE_PHP_SECRET'),

    'api_url' => env('NATIVE_PHP_API_URL', 'http://localhost:4000/api/'),

    'hot_reload' => [
        base_path('app/Providers/NativeAppServiceProvider.php'),
    ],

    'provider' => \App\Providers\NativeAppServiceProvider::class,
];
