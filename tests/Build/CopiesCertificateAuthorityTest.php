<?php

use Native\Desktop\Drivers\Electron\ElectronServiceProvider;
use Native\Support\Builder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

it('can copy the default CA certificate from php-bin', function () {

    $certificatePath = Path::join(ElectronServiceProvider::ELECTRON_PATH, 'resources', 'cacert.pem');
    (new Filesystem)->remove($certificatePath);

    expect($certificatePath)->not->toBeFile();

    resolve(Builder::class)->copyCertificateAuthority(path: ElectronServiceProvider::ELECTRON_PATH.'/resources');

    expect($certificatePath)->toBeFile();

});
