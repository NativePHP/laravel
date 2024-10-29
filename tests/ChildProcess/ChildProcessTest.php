<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Native\Laravel\Facades\ChildProcess;

beforeEach(function () {
    Http::fake();
});

it('can start a child process', function () {
    ChildProcess::start('foo bar', 'some-alias', 'path/to/dir', ['baz' => 'zah']);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'http://localhost:4000/api/child-process/start' &&
               $request['alias'] === 'some-alias' &&
               $request['cmd'] === ['foo', 'bar'] &&
               $request['cwd'] === 'path/to/dir' &&
               $request['env'] === ['baz' => 'zah'];
    });
});

it('can start a artisan command', function () {
    ChildProcess::artisan('foo:bar', 'some-alias', ['baz' => 'zah']);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'http://localhost:4000/api/child-process/start' &&
               $request['alias'] === 'some-alias' &&
               $request['cmd'] === [PHP_BINARY, 'artisan', 'foo:bar'] &&
               $request['cwd'] === base_path() &&
               $request['env'] === ['baz' => 'zah'];
    });
});

it('can mark the process as persistent')->todo();

it('accepts either a string or a array as start command argument', function () {
    ChildProcess::start('foo bar', 'some-alias');
    Http::assertSent(fn (Request $request) => $request['cmd'] === ['foo', 'bar']);

    ChildProcess::start(['foo', 'baz'], 'some-alias');
    Http::assertSent(fn (Request $request) => $request['cmd'] === ['foo', 'baz']);
});

it('accepts either a string or a array as artisan command argument', function () {
    ChildProcess::artisan('foo:bar', 'some-alias');
    Http::assertSent(fn (Request $request) => $request['cmd'] === [PHP_BINARY, 'artisan', 'foo:bar']);

    ChildProcess::artisan(['foo:baz'], 'some-alias');
    Http::assertSent(fn (Request $request) => $request['cmd'] === [PHP_BINARY, 'artisan', 'foo:baz']);
});

it('sets the cwd to the base path if none was given', function () {
    ChildProcess::start(['foo', 'bar'], 'some-alias', cwd: 'path/to/dir');
    Http::assertSent(fn (Request $request) => $request['cwd'] === 'path/to/dir');

    ChildProcess::start(['foo', 'bar'], 'some-alias');
    Http::assertSent(fn (Request $request) => $request['cwd'] === base_path());
});

it('filters double spaces when exploding a command string', function () {
    ChildProcess::start('foo bar  baz      bak', 'some-alias');
    Http::assertSent(fn (Request $request) => $request['cmd'] === ['foo', 'bar', 'baz', 'bak']);
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
               $request['message'] === '"some-message"';
    });
});
