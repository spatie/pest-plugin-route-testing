<?php

namespace Spatie\RouteTesting;

use Illuminate\Support\Arr;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Testing\TestResponse;
use Pest\PendingCalls\TestCall;
use Pest\Support\Backtrace;
use Pest\TestSuite;

/** @mixin TestCall */
class RouteTestingTestCall
{
    use ForwardsCalls;

    protected TestCall $testCall;

    protected RouteResolver $routeResolver;

    protected RouteTester $routeTester;

    public function __construct(TestCall $testCall, RouteTester $routeTester)
    {
        $this->testCall = $testCall;

        $this->routeTester = $routeTester;

        $this->routeResolver = new RouteResolver();

        $this->with($this->routeResolver->getFilteredRouteList());
    }

    protected function with(array $routes): self
    {
        $this->testCall->testCaseMethod->datasets = [$routes];

        return $this;
    }

    /**
     * @param string[]|array $path
     * @return $this
     */
    public function exclude(string|array $path): self
    {
        $this->routeResolver->exceptPaths(Arr::wrap($path));

        $this->with($this->routeResolver->getFilteredRouteList());

        return $this;
    }

    public function assertStatus(int $status): self
    {
        $this->routeTester->assert($status);

        return $this;
    }

    /**
     * @param array|string[] $path
     * @return $this
     */
    public function include(array|string $path): self
    {
        $this->routeResolver->paths(Arr::wrap($path));

        $this->with($this->routeResolver->getFilteredRouteList());

        return $this;
    }

    public function __call($method, $parameters)
    {
        return $this->forwardDecoratedCallTo($this->testCall, $method, $parameters);
    }
}
