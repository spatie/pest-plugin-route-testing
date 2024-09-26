<?php

use Pest\Matchers\Any;
use PHPUnit\Framework\Assert;
use Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

expect()->extend('toContainRoutes', function (array $expectedRoutes) {
    $routesNames = collect($this->value)->map(fn ($route) => $route['uri'])->values()->toArray();

    Assert::assertEqualsCanonicalizing($expectedRoutes, $routesNames);
});

expect()->extend('toHaveProtectedProperty', function (string $name, mixed $value = new Any, string $message = '') {
    $this->toBeObject();

    Assert::assertTrue(property_exists($this->value, $name), $message);

    $reflection = new ReflectionClass($this->value);
    $property = $reflection->getProperty($name);
    $property->setAccessible(true);

    Assert::assertTrue($property->isProtected(), $message);

    if (! $value instanceof Any) {
        Assert::assertEquals($value, $property->getValue($this->value), $message);
    }

    return $this;
});
