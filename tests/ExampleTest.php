<?php

use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\PhpExecutableFinder;

it('can boot up the app', function () {
    $executableFinder = new PhpExecutableFinder();
    $php = $executableFinder->find(false);

    copy(
        from: __DIR__.'/fixtures/artisan',
        to: base_path('artisan'),
    );

    $process = Process::path(base_path())
        ->tty()
        ->start($php.' artisan native:serve', function ($type, $line) {
            echo $line;
        });

    try {
        retry(12, function () {
            // Wait until port 8100 is open
            dump('Waiting for port 8100 to open...');
            $fp = @fsockopen('localhost', 8100, $errno, $errstr, 1);
            if ($fp === false) {
                throw new Exception('Port 8100 is not open yet');
            }
        }, 5000);
    } catch (Exception $e) {
        Process::run('pkill -9 -P '.$process->id());
        throw $e;
    }

    Process::run('pkill -9 -P '.$process->id());

    expect(true)->toBeTrue();
});
