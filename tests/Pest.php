<?php

use PHPUnit\Framework\Assert;
use Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

expect()->extend('toContainRoutes', function (array $expectedRoutes) {
    $routesNames = collect($this->value)->map(fn ($route) => $route['uri'])->toArray();

    Assert::assertEqualsCanonicalizing($expectedRoutes, $routesNames);
});
