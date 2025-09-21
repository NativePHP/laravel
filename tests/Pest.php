<?php

use Native\Laravel\Tests\TestCase;
use Symfony\Component\Filesystem\Filesystem;

uses(TestCase::class)->in(__DIR__);

function testsDir(string $path = ''): string
{
    return __DIR__.'/'.$path;
}

function createFiles(array|string $paths): array
{
    $paths = (array) $paths;
    $filesystem = new Filesystem;

    foreach ($paths as $path) {
        $filesystem->dumpFile($path, '');
    }

    return $paths;
}
