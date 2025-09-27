<?php

use Native\Desktop\Builder\Builder;
use Symfony\Component\Filesystem\Filesystem;

it('can copy the default CA certificate from php-bin', function () {

    $builder = resolve(Builder::class);
    $certificatePath = $builder->buildPath('cacert.pem');
    (new Filesystem)->remove($certificatePath);

    expect($certificatePath)->not->toBeFile();

    $builder->copyCertificateAuthority();

    expect($certificatePath)->toBeFile();

});
