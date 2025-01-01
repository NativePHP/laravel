<?php

namespace Native\Laravel\Listeners;

use Illuminate\Support\Facades\Pipeline;
use Native\Laravel\Events\App\ProjectFileChanged;
use Webmozart\Assert\Assert;

class ProjectFileChangedListener
{
    public function handle(ProjectFileChanged $event): void
    {
        foreach ($pipelines = config('nativephp.on_php_file_change') as $class) {
            Assert::classExists($class, "Class {$class} does not exist");
        }

        Pipeline::send($event)->through($pipelines)->thenReturn();
    }
}
