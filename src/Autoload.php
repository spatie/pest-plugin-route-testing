<?php

declare(strict_types=1);

namespace Spatie\RouteTesting;

use Pest\Plugin;

Plugin::uses(RouteTestable::class);

if (! function_exists('routeTesting')) {
    function routeTesting(string $description)
    {
        return new RouteTestingTestCall(test($description));
    }
}
