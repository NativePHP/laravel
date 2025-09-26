<?php

use Native\Desktop\Builder\Builder;
use Native\Desktop\Drivers\Electron\ElectronServiceProvider;
use Symfony\Component\Filesystem\Filesystem;

it('can copy the default CA certificate from php-bin', function () {

    $certificatePath = ElectronServiceProvider::buildPath('cacert.pem');
    (new Filesystem)->remove($certificatePath);

    expect($certificatePath)->not->toBeFile();

    resolve(Builder::class)->copyCertificateAuthority(path: ElectronServiceProvider::buildPath());

    expect($certificatePath)->toBeFile();

});
