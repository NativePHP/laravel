<?php

return [
    'running' => env('NATIVE_PHP_RUNNING', false),

    'storage_path' => env('NATIVE_PHP_STORAGE_PATH'),

    'database_path' => env('NATIVE_PHP_DATABASE_PATH'),

    'version' => env('NATIVE_PHP_VERSION', '1.0.0'),

    'secret' => env('NATIVE_PHP_SECRET'),

    'app_id' => env('NATIVEPHP_APP_ID'),

    'deeplink_scheme' => env('NATIVEPHP_DEEPLINK_SCHEME'),

    'author' => env('NATIVEPHP_APP_AUTHOR'),

    'api_url' => env('NATIVE_PHP_API_URL', 'http://localhost:4000/api/'),

    'provider' => \App\Providers\NativeAppServiceProvider::class,
];
