<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Native\Laravel\ChildProcess as ChildProcessImplement;
use Native\Laravel\Client\Client;
use Native\Laravel\Facades\ChildProcess;

beforeEach(function () {
    Http::fake();

    $mock = Mockery::mock(ChildProcessImplement::class, [resolve(Client::class)])
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $this->instance(ChildProcessImplement::class, $mock->allows([
        'fromRuntimeProcess' => $mock,
    ]));
});

it('can start a child process', function () {
    ChildProcess::start('foo bar', 'some-alias', 'path/to/dir', ['baz' => 'zah']);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'http://localhost:4000/api/child-process/start' &&
               $request['alias'] === 'some-alias' &&
               $request['cmd'] === ['foo bar'] &&
               $request['cwd'] === 'path/to/dir' &&
               $request['env'] === ['baz' => 'zah'];
    });
});

it('can start a php command', function () {
    ChildProcess::php("-r 'sleep(5);'", 'some-alias', ['baz' => 'zah']);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'http://localhost:4000/api/child-process/start-php' &&
               $request['alias'] === 'some-alias' &&
               $request['cmd'] === ["-r 'sleep(5);'"] &&
               $request['cwd'] === base_path() &&
               $request['env'] === ['baz' => 'zah'];
    });
});

it('can start a artisan command', function () {
    ChildProcess::artisan('foo:bar --verbose', 'some-alias', ['baz' => 'zah']);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'http://localhost:4000/api/child-process/start-php' &&
               $request['alias'] === 'some-alias' &&
               $request['cmd'] === ['artisan', 'foo:bar --verbose'] &&
               $request['cwd'] === base_path() &&
               $request['env'] === ['baz' => 'zah'];
    });
});

it('accepts either a string or a array as start command argument', function () {
    ChildProcess::start('foo bar', 'some-alias');
    Http::assertSent(fn (Request $request) => $request['cmd'] === ['foo bar']);

    ChildProcess::start(['foo', 'baz'], 'some-alias');
    Http::assertSent(fn (Request $request) => $request['cmd'] === ['foo', 'baz']);
});

it('accepts either a string or a array as php command argument', function () {
    ChildProcess::php("-r 'sleep(5);'", 'some-alias');
    Http::assertSent(fn (Request $request) => $request['cmd'] === ["-r 'sleep(5);'"]);

    ChildProcess::php(['-r', "'sleep(5);'"], 'some-alias');
    Http::assertSent(fn (Request $request) => $request['cmd'] === ['-r', "'sleep(5);'"]);
});

it('accepts either a string or a array as artisan command argument', function () {
    ChildProcess::artisan('foo:bar', 'some-alias');
    Http::assertSent(fn (Request $request) => $request['cmd'] === ['artisan', 'foo:bar']);

    ChildProcess::artisan(['foo:baz'], 'some-alias');
    Http::assertSent(fn (Request $request) => $request['cmd'] === ['artisan', 'foo:baz']);
});

it('sets the cwd to the base path if none was given', function () {
    ChildProcess::start(['foo', 'bar'], 'some-alias', cwd: 'path/to/dir');
    Http::assertSent(fn (Request $request) => $request['cwd'] === 'path/to/dir');

    ChildProcess::start(['foo', 'bar'], 'some-alias');
    Http::assertSent(fn (Request $request) => $request['cwd'] === base_path());
});

it('can stop a child process', function () {
    ChildProcess::stop('some-alias');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'http://localhost:4000/api/child-process/stop' &&
               $request['alias'] === 'some-alias';
    });
});

it('can send messages to a child process', function () {
    ChildProcess::message('some-message', 'some-alias');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'http://localhost:4000/api/child-process/message' &&
               $request['alias'] === 'some-alias' &&
               $request['message'] === 'some-message';
    });
});

it('can mark a process as persistent', function () {
    ChildProcess::start('foo bar', 'some-alias', persistent: true);
    Http::assertSent(fn (Request $request) => $request['persistent'] === true);
});

it('can mark a php command as persistent', function () {
    ChildProcess::php("-r 'sleep(5);'", 'some-alias', persistent: true);
    Http::assertSent(fn (Request $request) => $request['persistent'] === true);
});

it('can mark a artisan command as persistent', function () {
    ChildProcess::artisan('foo:bar', 'some-alias', persistent: true);
    Http::assertSent(fn (Request $request) => $request['persistent'] === true);
});

it('marks the process as non-persistent by default', function () {
    ChildProcess::start('foo bar', 'some-alias');
    Http::assertSent(fn (Request $request) => $request['persistent'] === false);
});
