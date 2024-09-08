<?php

use PHPUnit\Framework\ExpectationFailedException;

use function Orchestra\Testbench\remote;

it('can boot up the app', function () {
    $output = '';

    $process = remote('native:serve --no-dependencies --no-interaction');
    $process->start(function ($type, $line) use (&$output) {
        $output .= $line;
    });

    try {
        retry(20, function () use ($output) {
            // Wait until port 8100 is open
            dump('Waiting for port 8100 to open...');

            $fp = @fsockopen('localhost', 8100, $errno, $errstr, 1);
            if ($fp === false) {
                throw new Exception(sprintf(
                    'Port 8100 is not open yet. Output: "%s"',
                    $output,
                ));
            }
        }, 5000);
    } finally {
        $process->stop();
    }

    try {
        expect($output)->toContain('Running the dev script with npm');
    } catch (ExpectationFailedException) {
        throw new ExpectationFailedException(sprintf(
            '"%s" does not match the expected output.',
            $output,
        ));
    }
});
