<?php

beforeAll(function () {
    // Set up the environment variables
    putenv('ZEPHPYR_KEY=dummy');
    putenv('ZEPHPYR_TOKEN=dummy');
});

afterAll(function () {
    // Clean up the temp folder
    foreach (glob(base_path('temp/app_*.zip')) as $zip) {
        unlink($zip);
    }
});

afterEach(function () {
    // Clean up the app folder recursively
    rmdir_recursive(base_path('resources/app'));
});

it('will remove laravel.log by default before bundling', function () {
    $logPath = 'resources/app/storage/logs';
    $laravelLog = $logPath.'/laravel.log';

    // Create a dummy copy of the file
    if (! file_exists($logPath)) {
        mkdir($logPath, 0755, true);
    }
    file_put_contents($laravelLog, 'TEST');

    // Run the test
    $this->artisan('native:bundle --without-cleanup')
        ->expectsOutput('Creating zip archive');

    // Assert
    expect(basename($laravelLog))
        ->not->toBeInZip(findLatestZipPath());
});

it('will remove the content folder by default before bundling', function () {
    $contentPath = 'resources/app/content';

    // Create a dummy copy of the folder
    if (! file_exists($contentPath)) {
        mkdir($contentPath, 0755, true);
    }

    // Run the test
    $this->artisan('native:bundle --without-cleanup')
        ->expectsOutput('Creating zip archive');

    // Assert
    expect($contentPath)
        ->not->toBeInZip(findLatestZipPath());
});

it('will remove only files that match a globbed path', function () {
    $wildcardPath = 'resources/app/wildcardPath';
    $yes1DeletePath = $wildcardPath.'/YES1.txt';
    $yes2DeletePath = $wildcardPath.'/YES2.txt';
    $noDeletePath = $wildcardPath.'/NO.txt';

    config()->set('nativephp.cleanup_exclude_files', [$wildcardPath.'/YES*']);

    // Create some dummy files
    if (! file_exists($wildcardPath)) {
        mkdir($wildcardPath, 0755, true);
    }
    file_put_contents($yes1DeletePath, 'PLEASE DELETE ME');
    file_put_contents($yes2DeletePath, 'PLEASE DELETE ME TOO');
    file_put_contents($noDeletePath, 'DO NOT DELETE ME');

    // Run the test
    $this->artisan('native:bundle --without-cleanup')
        ->expectsOutput('Creating zip archive');

    // Assert
    $latestZip = findLatestZipPath();

    expect($yes1DeletePath)
        ->not->toBeInZip($latestZip);
    expect($yes2DeletePath)
        ->not->toBeInZip($latestZip);
    expect($noDeletePath)
        ->toBeInZip($latestZip);

});
