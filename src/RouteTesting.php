<?php

declare(strict_types=1);

namespace Spatie\RouteTesting;

/**
 * @internal
 */
trait RouteTesting
{
    public function routeTesting(string $name): static
    {
        expect($name)->toBeString();

        return $this;
    }
}
