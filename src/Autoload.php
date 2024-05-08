<?php

declare(strict_types=1);

namespace Spatie\RouteTesting;

use Pest\Plugin;
use PHPUnit\Framework\TestCase;

Plugin::uses(RouteTestable::class);

if (! function_exists('routeTesting')) {
    function routeTesting(): RouteTesting
    {
        //test()->withExceptionHandling();

        return test()->routeTesting(...func_get_args()); // @phpstan-ignore-line
    }
}
