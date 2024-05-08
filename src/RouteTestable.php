<?php

declare(strict_types=1);

namespace Spatie\RouteTesting;

/**
 * @internal
 */
trait RouteTestable
{
    public function routeTesting(): RouteTesting
    {
        return new RouteTesting();
    }
}
