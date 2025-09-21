<?php

namespace Native\Electron\Tests\Unit\Traits;

use Native\Electron\Traits\CopiesCertificateAuthority;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

it('can copy the default CA certificate from php-bin', function ($mock) {
    // Set up
    app()->setBasePath(realpath(__DIR__.'/../../../'));

    // / Make directory temporarily
    mkdir(base_path('vendor/nativephp/electron/resources/js/resources'), 0777, true);

    // Test
    expect(file_exists(base_path('vendor/nativephp/electron/resources/js/resources/cacert.pem')))->toBeFalse();

    $mock->run();

    expect(file_exists(base_path('vendor/nativephp/electron/resources/js/resources/cacert.pem')))->toBeTrue();

    // Cleanup
    // Delete the vendor/nativephp/electron directory, recursively including directories and then files
    $files = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator(base_path('vendor/nativephp/electron'), RecursiveDirectoryIterator::SKIP_DOTS),
            fn ($current, $key, $iterator) => $current->isDir() || $current->isFile()
        ),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $file) {
        if ($file->isDir()) {
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }

    rmdir(base_path('vendor/nativephp/electron'));

})->with([
    // Empty class with the CopiesCertificateAuthority trait
    new class
    {
        use CopiesCertificateAuthority;

        public function run()
        {
            $this->copyCertificateAuthorityCertificate();
        }
    },
]);
