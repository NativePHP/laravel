<?php

namespace Native\Laravel\Exceptions;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    protected $internalDontReport = [];

    public function register(): void
    {
        $this->reportable(function (\Throwable $e) {
            error_log("[NATIVE_EXCEPTION]: {$e->getMessage()} ({$e->getCode()}) in {$e->getFile()}:{$e->getLine()}");
        });
    }
}
