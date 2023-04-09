<?php

return [
    'secret' => env('NATIVE_PHP_SECRET'),

    'api_url' => env('NATIVE_PHP_API_URL', 'http://localhost:4000/api/'),

    'provider' => \App\Providers\NativeAppServiceProvider::class,
];
