<?php

use Native\Electron\Traits\CleansEnvFile;
use Native\Laravel\NativeServiceProvider;
use Symfony\Component\Filesystem\Filesystem;

/*
|--------------------------------------------------------------------------
| Setup
|--------------------------------------------------------------------------
*/
$buildPath = testsDir('_test_build_path');

beforeEach(function () use ($buildPath) {
    (new Filesystem)->remove($buildPath);

    // Need to register this or the nativephp config won't merge.
    // A case to move this to the other repo altogether?
    app()->register(NativeServiceProvider::class);
});
afterEach(fn () => (new Filesystem)->remove($buildPath));

/*
|--------------------------------------------------------------------------
| Mock Build command with anonymous class
|--------------------------------------------------------------------------
*/
$command = new class($buildPath)
{
    use CleansEnvFile;

    public function __construct(
        public $buildPath
    ) {}

    protected function buildPath(string $path = ''): string
    {
        return app()->joinPaths($this->buildPath, $path);
    }
};

/*
|--------------------------------------------------------------------------
| Tests
|--------------------------------------------------------------------------
*/
it('cleans configured keys', function () use ($buildPath, $command) {

    (new Filesystem)->dumpFile("{$buildPath}/.env", <<<'TXT'
    FOO=BAR
    BAZ=ZAH
    TXT);

    config()->set('nativephp.cleanup_env_keys', [
        'FOO',
    ]);

    $command->cleanEnvFile();

    expect(file_get_contents("{$buildPath}/.env"))
        ->not->toContain('FOO')
        ->toContain('BAZ');
});

it('removes comments', function () use ($buildPath, $command) {

    (new Filesystem)->dumpFile("{$buildPath}/.env", <<<'TXT'
    KEEP_ME=hello
    # REMOVE_ME=hello
    TXT);

    $command->cleanEnvFile();

    expect(file_get_contents("{$buildPath}/.env"))
        ->not->toContain('REMOVE_ME')
        ->toContain('KEEP_ME');
});

it('injects defaults', function () use ($buildPath, $command) {
    (new Filesystem)->dumpFile("{$buildPath}/.env", <<<'TXT'
    LOG_CHANNEL=test
    LOG_STACK=test
    TXT);

    $command->cleanEnvFile();

    expect(file_get_contents("{$buildPath}/.env"))
        ->not->toContain('LOG_CHANNEL=test')
        ->not->toContain('LOG_STACK=test')
        ->toContain('LOG_CHANNEL=stack')
        ->toContain('LOG_STACK=daily')
        ->toContain('LOG_DAILY_DAYS');
});

it('cleans default cleanup keys', function () use ($buildPath, $command) {

    // NOTE: This checks the default cleanup_env_keys are cleaned. So we can sleep at night.
    (new Filesystem)->dumpFile("{$buildPath}/.env", <<<'TXT'
    SAFE_VARIABLE=test
    AWS_WILDCARD=test
    GITHUB_WILDCARD=test
    DO_SPACES_WILDCARD=test
    WILDCARD_SECRET=test
    NATIVEPHP_UPDATER_PATH=test
    NATIVEPHP_APPLE_ID=test
    NATIVEPHP_APPLE_ID_PASS=test
    NATIVEPHP_APPLE_TEAM_ID=test
    TXT);

    $command->cleanEnvFile();

    expect(file_get_contents("{$buildPath}/.env"))
        ->toContain('SAFE_VARIABLE=test')
        ->not->toContain('AWS_WILDCARD')
        ->not->toContain('GITHUB_WILDCARD')
        ->not->toContain('DO_SPACES_WILDCARD')
        ->not->toContain('WILDCARD_SECRET')
        ->not->toContain('NATIVEPHP_UPDATER_PATH')
        ->not->toContain('NATIVEPHP_APPLE_ID')
        ->not->toContain('NATIVEPHP_APPLE_ID_PASS')
        ->not->toContain('NATIVEPHP_APPLE_TEAM_ID');
})->skip('This test fails in CI when with composer --prefer-lowest. The config that is loaded via the NativeServiceProvider on the Laravel repo does not exist in lower versions. TODO: Consider moving the logic & tests to the laravel repo instead?');
