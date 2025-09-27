<?php

use Native\Desktop\Builder\Concerns\CopiesToBuildDirectory;
use Symfony\Component\Filesystem\Filesystem;

/*
|--------------------------------------------------------------------------
| Setup
|--------------------------------------------------------------------------
*/
$sourcePath = testsDir('_test_source_path');
$buildPath = testsDir('_test_build_path');

beforeEach(function () use ($sourcePath, $buildPath) {
    $filesystem = new Filesystem;
    $filesystem->mkdir($sourcePath);
    $filesystem->remove($buildPath);
});

afterEach(function () use ($sourcePath, $buildPath) {
    $filesystem = new Filesystem;
    $filesystem->remove($sourcePath);
    $filesystem->remove($buildPath);
});

/*
|--------------------------------------------------------------------------
| Mock Build command with anonymous class
|--------------------------------------------------------------------------
*/
$command = new class($sourcePath, $buildPath)
{
    use CopiesToBuildDirectory;

    public function __construct(
        public $sourcePath,
        public $buildPath
    ) {}

    public function sourcePath(string $path = ''): string
    {
        return app()->joinPaths($this->sourcePath, $path);
    }

    public function buildPath(string $path = ''): string
    {
        return app()->joinPaths($this->buildPath, $path);
    }
};

/*
|--------------------------------------------------------------------------
| Tests
|--------------------------------------------------------------------------
*/
it('will remove the build directory by default before copying', function () use ($buildPath, $command) {

    createFiles("$buildPath/app/test.txt");

    expect("$buildPath/app/test.txt")->toBeFile();

    $command->copyToBuildDirectory();

    expect("$buildPath/app/test.txt")->not->toBeFile();
});

it('copies included files and directories', function () use ($sourcePath, $buildPath, $command) {

    createFiles([
        "$sourcePath/do-not-delete.txt",
        "$sourcePath/foo-bar/do-not-delete.json",
    ]);

    $command->copyToBuildDirectory();

    expect([
        "$buildPath/app/do-not-delete.txt",
        "$buildPath/app/foo-bar/do-not-delete.json",
    ])->each->toBeFile();
});

it('skips directories by path', function () use ($sourcePath, $buildPath, $command) {
    createFiles("$sourcePath/foo-bar/do-not-delete.json");

    config()->set('nativephp.cleanup_exclude_files', [
        'foo-bar',
    ]);

    $command->copyToBuildDirectory();

    expect("$buildPath/app/foo-bar")->not->toBeDirectory();
    expect("$buildPath/app/foo-bar")->not->toBeDirectory();
});

it('skips files by path', function () use ($sourcePath, $buildPath, $command) {
    createFiles("$sourcePath/foo-bar/delete-me.json");

    config()->set('nativephp.cleanup_exclude_files', [
        'foo-bar/delete-me.json',
    ]);

    $command->copyToBuildDirectory();

    expect("$buildPath/app/foo-bar")->toBeDirectory();
    expect("$buildPath/app/foo-bar/delete-me.json")->not->toBeFile();
});

it('skips directories by wildcard path', function () use ($sourcePath, $buildPath, $command) {
    createFiles([
        "$sourcePath/dont-delete/foo.json",
        "$sourcePath/do/delete/foo.json",
    ]);

    config()->set('nativephp.cleanup_exclude_files', [
        'do/*',
    ]);

    $command->copyToBuildDirectory();

    expect("$buildPath/app/dont-delete")->toBeDirectory();
    expect("$buildPath/app/do")->toBeDirectory();
    expect("$buildPath/app/do/delete")->not->toBeDirectory();
});

it('skips files by wildcard path', function () use ($sourcePath, $buildPath, $command) {
    createFiles([
        "$sourcePath/foo/remove.json",
        "$sourcePath/foo/dont-remove.php",
        "$sourcePath/bar/remove.json",
    ]);

    config()->set('nativephp.cleanup_exclude_files', [
        '*.json',
    ]);

    $command->copyToBuildDirectory();

    expect("$buildPath/app/foo/remove.json")->not->toBeFile();
    expect("$buildPath/app/bar/remove.json")->not->toBeFile();
    expect("$buildPath/app/foo/dont-remove.php")->toBeFile();
});

it('skips matches on any number of subdirectories', function () use ($sourcePath, $buildPath, $command) {
    createFiles([
        "$sourcePath/matches/subdir/remove.json",
        "$sourcePath/matches/any/subdir/remove.json",
        "$sourcePath/matches/any/subdir/dont-remove.php",
    ]);

    config()->set('nativephp.cleanup_exclude_files', [
        '**/*.json',
    ]);

    $command->copyToBuildDirectory();

    expect("$buildPath/app/matches/subdir/remove.json")->not->toBeFile();
    expect("$buildPath/app/matches/any/subdir/remove.json")->not->toBeFile();
    expect("$buildPath/app/matches/any/subdir/dont-remove.php")->toBeFile();
});

it('will never include files that may contain sensitive information', function () use ($sourcePath, $buildPath, $command) {

    $sourceFiles = createFiles([
        "$sourcePath/do-not-delete.txt",
        "$sourcePath/database/wildcard.sqlite",
        "$sourcePath/database/wildcard.sqlite-shm",
        "$sourcePath/database/wildcard.sqlite-wal",
        "$sourcePath/storage/framework/sessions/wildcard.txt",
        "$sourcePath/storage/framework/testing/wildcard.txt",
        "$sourcePath/storage/framework/cache/wildcard.txt",
        "$sourcePath/storage/framework/views/wildcard.txt",
        "$sourcePath/storage/logs/wildcard.log",
    ]);

    expect($sourceFiles)->each->toBeFile();

    $command->copyToBuildDirectory();

    expect([
        "$buildPath/app/database/wildcard.sqlite",
        "$buildPath/app/database/wildcard.sqlite-shm",
        "$buildPath/app/database/wildcard.sqlite-wal",
        "$buildPath/app/storage/framework/sessions/wildcard.txt",
        "$buildPath/app/storage/framework/testing/wildcard.txt",
        "$buildPath/app/storage/framework/cache/wildcard.txt",
        "$buildPath/app/storage/framework/views/wildcard.txt",
        "$buildPath/app/storage/logs/wildcard.log",
    ])->each->not->toBeFile();

    expect("$buildPath/app/do-not-delete.txt")->toBeFile();
});

it('makes sure required folders are not empty', function () use ($buildPath, $command) {

    $required = [
        "{$buildPath}/app/storage/framework/cache/_native.json",
        "{$buildPath}/app/storage/framework/sessions/_native.json",
        "{$buildPath}/app/storage/framework/testing/_native.json",
        "{$buildPath}/app/storage/framework/views/_native.json",
        "{$buildPath}/app/storage/app/public/_native.json",
        "{$buildPath}/app/storage/logs/_native.json",
    ];

    expect($required)->each->not->toBeFile();

    $command->copyToBuildDirectory();

    expect($required)->each->toBeFile();
});

it('preserves file permissions', function () use ($sourcePath, $buildPath, $command) {
    createFiles("$sourcePath/file-under-test.txt");

    chmod("$sourcePath/file-under-test.txt", octdec('0775'));

    $originalPermissions = fileperms("$sourcePath/file-under-test.txt");

    $command->copyToBuildDirectory();

    expect(fileperms("$buildPath/app/file-under-test.txt"))->toBe($originalPermissions);
});
