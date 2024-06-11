<?php

namespace Spatie\RouteTesting;

use Closure;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Routing\Route;
use Illuminate\Testing\TestResponse;
use Pest\PendingCalls\BeforeEachCall;
use Pest\PendingCalls\TestCall;
use Pest\Support\Backtrace;
use Pest\TestSuite;
use Illuminate\Support\Facades\Route as RouteFacade;

class RouteTest
{
    public static function bind(string $binding, Closure $closure): void
    {
        new BeforeEachCall(
            TestSuite::getInstance(),
            Backtrace::testFile(),
            fn() => $this->parameters[$binding] = $closure()
        );
    }

    public static function test(TestCall $test, array $assertions): void
    {
        /** @var TestResponse $testResponse */
        $testResponse = $test
            ->defer(function (string $method, string $uri) {
                /** @var Route $route */
                $route = collect(RouteFacade::getRoutes()
                    ->getRoutesByMethod()[$method])
                    ->first(fn(Route $route) => $route->uri === $uri);

                if ($route === null) {
                    $this->markTestIncomplete("Route not found: {$method} {$uri}");
                }

                try {
                    $this->url = url()->toRoute($route, $this->parameters, false);
                } catch (UrlGenerationException) {
                    $this->markTestSkipped("Missing parameters for route: {$method} {$uri}");
                }
            })
        ->expect(fn(string $method, string $uri) => $this->{$method}($this->url));

        foreach ($assertions as [$method, $parameters]) {
            $testResponse->{$method}(...$parameters);
        }
    }
}
