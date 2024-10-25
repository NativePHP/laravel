<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Native\Laravel\Facades\ChildProcess;

beforeEach(function () {
    Http::fake();
});

it('can start a child process', function () {
    ChildProcess::start('some-alias', 'foo bar', 'path/to/dir', ['baz' => 'zah']);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'http://localhost:4000/api/child-process/start' &&
               $request['alias'] === 'some-alias' &&
               $request['cmd'] === ['foo', 'bar'] &&
               $request['cwd'] === 'path/to/dir' &&
               $request['env'] === ['baz' => 'zah'];
    });
});

it('can start a artisan command', function () {
    ChildProcess::artisan('some-alias', 'foo:bar', ['baz' => 'zah']);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'http://localhost:4000/api/child-process/start' &&
               $request['alias'] === 'some-alias' &&
               $request['cmd'] === [str_replace(' ', '\ ', PHP_BINARY), 'artisan', 'foo:bar'] &&
               $request['cwd'] === base_path() &&
               $request['env'] === ['baz' => 'zah'];
    });
});

it('can mark the process as persistent')->todo();

it('accepts either a string or a array as start command argument', function () {
    ChildProcess::start('some-alias', 'foo bar');
    Http::assertSent(fn (Request $request) => $request['cmd'] === ['foo', 'bar']);

    ChildProcess::start('some-alias', ['foo', 'baz']);
    Http::assertSent(fn (Request $request) => $request['cmd'] === ['foo', 'baz']);
});

it('accepts either a string or a array as artisan command argument', function () {
    ChildProcess::artisan('some-alias', 'foo:bar');
    Http::assertSent(fn (Request $request) => $request['cmd'] === [str_replace(' ', '\ ', PHP_BINARY), 'artisan', 'foo:bar']);

    ChildProcess::artisan('some-alias', ['foo:baz']);
    Http::assertSent(fn (Request $request) => $request['cmd'] === [str_replace(' ', '\ ', PHP_BINARY), 'artisan', 'foo:baz']);
});

it('sets the cwd to the base path if none was given', function () {
    ChildProcess::start('some-alias', ['foo', 'bar'], cwd: 'path/to/dir');
    Http::assertSent(fn (Request $request) => $request['cwd'] === 'path/to/dir');

    ChildProcess::start('some-alias', ['foo', 'bar']);
    Http::assertSent(fn (Request $request) => $request['cwd'] === base_path());
});

it('filters double spaces when exploding a command string', function () {
    ChildProcess::start('some-alias', 'foo bar  baz      bak');
    Http::assertSent(fn (Request $request) => $request['cmd'] === ['foo', 'bar', 'baz', 'bak']);
});

it('escapes spaces when passing a command array', function () {
    ChildProcess::start('some-alias', ['path/to/some executable with spaces.sh', '--foo', '--bar']);
    Http::assertSent(fn (Request $request) => $request['cmd'] === ['path/to/some\ executable\ with\ spaces.sh', '--foo', '--bar']);
});

it('can stop a child process', function () {
    ChildProcess::stop('some-alias');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'http://localhost:4000/api/child-process/stop' &&
               $request['alias'] === 'some-alias';
    });
});

it('can send messages to a child process', function () {
    ChildProcess::message('some-alias', 'some-message');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'http://localhost:4000/api/child-process/message' &&
               $request['alias'] === 'some-alias' &&
               $request['message'] === '"some-message"';
    });
});
