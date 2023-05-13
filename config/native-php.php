<?php

return [
    'version' => env('NATIVE_PHP_VERSION', '1.0.0'),

    'secret' => env('NATIVE_PHP_SECRET'),

    'app_id' => env('NATIVEPHP_APP_ID'),

    'author' => env('NATIVEPHP_APP_AUTHOR'),

    'hot_reload' => [
        base_path('app/Providers/NativeAppServiceProvider.php'),
    ],

    'provider' => \App\Providers\NativeAppServiceProvider::class,
];
