<?php

use Native\Electron\ElectronServiceProvider;
use Native\Electron\Traits\CopiesCertificateAuthority;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

it('can copy the default CA certificate from php-bin', function ($mock) {

    $certificatePath = Path::join(ElectronServiceProvider::ELECTRON_PATH, 'resources', 'cacert.pem');
    (new Filesystem)->remove($certificatePath);

    expect($certificatePath)->not->toBeFile();

    $mock->run();

    expect($certificatePath)->toBeFile();

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
