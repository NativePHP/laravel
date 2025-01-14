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
     * The default list of files to exclude from the build
     */
    'cleanup_exclude_files' => [
        // .git and dev directories
        '.git',
        'dist',
        'docker',
        'packages',
        '**/.github',

        // Potentially containing sensitive info
        'database/*.sqlite',
        'database/*.sqlite-shm',
        'database/*.sqlite-wal',

        'storage/framework/sessions/*',
        'storage/framework/testing/*',
        'storage/framework/cache/*',
        'storage/framework/views/*',
        'storage/logs/*',

        // Only needed for local testing
        'vendor/nativephp/electron/resources',
        'vendor/nativephp/electron/vendor',
        'vendor/nativephp/electron/bin',
        'vendor/nativephp/laravel/vendor',
        'vendor/nativephp/php-bin',

        // Also deleted in PrunesVendorDirectory after fresh composer install
        'vendor/bin'
    ]
];
