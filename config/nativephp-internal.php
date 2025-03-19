<?php

return [
    /**
     * An internal flag to indicate if the app is running in the NativePHP
     * environment. This is used to determine if the app should use the
     * NativePHP database and storage paths.
     */
    'running' => env('NATIVEPHP_RUNNING', false),

    /**
     * The path to the NativePHP storage directory. This is used to store
     * uploaded files and other data.
     */
    'storage_path' => env('NATIVEPHP_STORAGE_PATH'),

    /**
     * The path to the NativePHP database directory. This is used to store
     * the SQLite database file.
     */
    'database_path' => env('NATIVEPHP_DATABASE_PATH'),

    /**
     * The secret key used to communicate with the NativePHP API.
     */
    'secret' => env('NATIVEPHP_SECRET'),

    /**
     * The URL to the NativePHP API.
     */
    'api_url' => env('NATIVEPHP_API_URL', 'http://localhost:4000/api/'),

    /**
     * Configuration for the Zephpyr API.
     */
    'zephpyr' => [
        'host' => env('ZEPHPYR_HOST', 'https://zephpyr.com'),
        'token' => env('ZEPHPYR_TOKEN'),
        'key' => env('ZEPHPYR_KEY'),
    ],

    /**
     * The credentials to use Apples Notarization service.
     */
    'notarization' => [
        'apple_id' => env('NATIVEPHP_APPLE_ID'),
        'apple_id_pass' => env('NATIVEPHP_APPLE_ID_PASS'),
        'apple_team_id' => env('NATIVEPHP_APPLE_TEAM_ID'),
    ],

    /**
     * The binary path of PHP for NativePHP to use at build.
     */
    'php_binary_path' => env('NATIVEPHP_PHP_BINARY_PATH'),
];
