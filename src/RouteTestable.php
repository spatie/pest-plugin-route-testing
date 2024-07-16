<?php

declare(strict_types=1);

namespace Spatie\RouteTesting;

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
