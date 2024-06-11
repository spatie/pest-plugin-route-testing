<?php

declare(strict_types=1);

namespace Spatie\RouteTesting;

use Closure;
use Pest\PendingCalls\BeforeEachCall;
use Pest\Support\Backtrace;
use Pest\TestSuite;

/**
 * @internal
 */
trait RouteTestable
{
    public function bind(string $binding, mixed $value): self
    {
        $this->parameters[$binding] = $value;

        return $this;
    }
}
