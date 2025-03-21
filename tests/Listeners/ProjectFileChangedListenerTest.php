<?php

use Illuminate\Contracts\Events\Dispatcher;
use Native\Laravel\Events\App\ProjectFileChanged;
use Native\Laravel\Tests\Fixtures\Fakes\FakePipeline;
use Webmozart\Assert\InvalidArgumentException as WebmozartInvalidArgumentException;

it('listens for the project file changed event and runs configured pipelines', function () {
    app()->singleton(FakePipeline::class, fn () => $fake = new FakePipeline);

    config(['nativephp.on_php_file_change' => [
        FakePipeline::class,
    ]]);

    app(Dispatcher::class)->dispatch(new ProjectFileChanged('some/file.php'));

    expect(app(FakePipeline::class)->handled)->toBeTrue();
    expect(app(FakePipeline::class)->carry)->toBeInstanceOf(ProjectFileChanged::class);
});

it('rejects nonexistent classes', function () {
    config(['nativephp.on_php_file_change' => [
        'definitely-not-a-class-fqcn',
    ]]);

    try {
        app(Dispatcher::class)->dispatch(new ProjectFileChanged('some/file.php'));
    } catch (WebmozartInvalidArgumentException $e) {
        expect($e->getMessage())->toBe('Class definitely-not-a-class-fqcn does not exist');

        return;
    }

    $this->fail('Expected an exception to be thrown');
});
