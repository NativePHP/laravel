<?php

namespace Native\Laravel\Tests\Fixtures\Fakes;

use Closure;

class FakePipeline
{
    public bool $handled = false;

    public mixed $carry;

    public function handle(mixed $carry, Closure $next)
    {
        $this->handled = true;
        $this->carry = $carry;

        return $next($carry);
    }
}
