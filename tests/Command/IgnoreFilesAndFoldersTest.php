<?php

it('will remove laravel.log by default before building', function () {
    $logPath = 'resources/app/storage/logs';
    $laravelLog = $logPath.'/laravel.log';

    // Create a dummy copy of the file
    if (! file_exists($logPath)) {
        mkdir($logPath, 0755, true);
    }
    file_put_contents($laravelLog, 'TEST');

    // Run the test
    $this->artisan('native:minify resources/app');
    $this->assertFalse(file_exists($laravelLog));

    // Clean up after ourselves
    if (file_exists($laravelLog)) {
        unlink($laravelLog);
    }
    if (file_exists('resources/app/storage/logs')) {
        rmdir('resources/app/storage/logs');
    }
    if (file_exists('resources/app/storage')) {
        rmdir('resources/app/storage');
    }
    removeAppFolder();
});

it('will remove the content folder by default before building', function () {
    $contentPath = 'resources/app/content';

    // Create a dummy copy of the folder
    if (! file_exists($contentPath)) {
        mkdir($contentPath, 0755, true);
    }

    // Run the test
    $this->artisan('native:minify resources/app');
    $this->assertFalse(file_exists($contentPath));

    // Clean up after ourselves
    if (file_exists($contentPath)) {
        unlink($contentPath);
    }
    removeAppFolder();
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
    $this->artisan('native:minify resources/app');
    $this->assertFalse(file_exists($yes1DeletePath));
    $this->assertFalse(file_exists($yes2DeletePath));
    $this->assertTrue(file_exists($noDeletePath));

    // Clean up after ourselves
    foreach ([$yes1DeletePath, $yes2DeletePath, $noDeletePath] as $remove) {
        if (file_exists($remove)) {
            unlink($remove);
        }
    }
    if (file_exists($wildcardPath)) {
        rmdir($wildcardPath);
    }
    removeAppFolder();
});

function removeAppFolder()
{
    if (file_exists('resources/app')) {
        rmdir('resources/app');
    }
}
