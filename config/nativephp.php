<?php

return [
    /**
     * The version of your app.
     * It is used to determine if the app needs to be updated.
     * Increment this value every time you release a new version of your app.
     */
    'version' => env('NATIVEPHP_APP_VERSION', '1.0.0'),

    'app_id' => env('NATIVEPHP_APP_ID'),

    'deeplink_scheme' => env('NATIVEPHP_DEEPLINK_SCHEME'),

    'author' => env('NATIVEPHP_APP_AUTHOR'),

    'api_url' => env('NATIVEPHP_API_URL', 'http://localhost:4000/api/'),

    'provider' => \App\Providers\NativeAppServiceProvider::class,
];
