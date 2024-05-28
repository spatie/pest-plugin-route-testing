<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\RouteTesting\RouteTestingServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            RouteTestingServiceProvider::class,
        ];
    }
}
