<?php

use Native\Laravel\Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeInZip', function (string $zipFile) {
    $zip = new ZipArchive;
    $zip->open($zipFile);

    $found = $zip->locateName($this->value) !== false;

    $zip->close();

    return $this->toBeTrue($found);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function rmdir_recursive($dir): void
{
    foreach (scandir($dir) as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        if (is_dir("$dir/$file")) {
            rmdir_recursive("$dir/$file");
        } else {
            unlink("$dir/$file");
        }
    }
    rmdir($dir);
}

function findLatestZipPath(): ?string
{
    $latestZip = null;
    $latestTime = 0;

    dump(glob(base_path('temp/app_*.zip')));
    foreach (glob(base_path('temp/app_*.zip')) as $zip) {
        $time = filemtime($zip);

        if ($time > $latestTime) {
            $latestTime = $time;
            $latestZip = $zip;
        }
    }

    return $latestZip;
}
