<?php

return [
    'running' => env('NATIVEPHP_RUNNING', false),

    'storage_path' => env('NATIVEPHP_STORAGE_PATH'),

    'database_path' => env('NATIVEPHP_DATABASE_PATH'),

    'version' => env('NATIVEPHP_APP_VERSION', '1.0.0'),

    'secret' => env('NATIVEPHP_SECRET'),

    'app_id' => env('NATIVEPHP_APP_ID'),

    'deeplink_scheme' => env('NATIVEPHP_DEEPLINK_SCHEME'),

    'author' => env('NATIVEPHP_APP_AUTHOR'),

    'api_url' => env('NATIVEPHP_API_URL', 'http://localhost:4000/api/'),

    'provider' => \App\Providers\NativeAppServiceProvider::class,
];
