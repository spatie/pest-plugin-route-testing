<?php

declare(strict_types=1);

namespace Spatie\RouteTesting;

use Illuminate\Support\Facades\Route;
use Pest\PendingCalls\TestCall;
use Pest\Plugin;
use Pest\Support\Backtrace;
use Pest\TestSuite;
use PHPUnit\Util\Test;

Plugin::uses(RouteTestable::class);

if (! function_exists('routeTesting')) {
    function routeTesting(string $description)
    {
        $test = test($description)
            ->expect(fn (string $method, string $uri) => $this->{$method}($uri));

        return new RouteTestingTestCall($test);
    }
}
