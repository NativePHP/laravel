<?php

return [
    'running' => env('NATIVE_PHP_RUNNING', false),

    'storage_path' => env('NATIVE_PHP_STORAGE_PATH'),

    'database_path' => env('NATIVE_PHP_DATABASE_PATH'),

    'secret' => env('NATIVE_PHP_SECRET'),

    'api_url' => env('NATIVE_PHP_API_URL', 'http://localhost:4000/api/'),

    'provider' => \App\Providers\NativeAppServiceProvider::class,
];
