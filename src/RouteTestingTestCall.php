<?php

namespace Spatie\RouteTesting;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Testing\TestResponse;
use Pest\PendingCalls\TestCall;

/** @mixin TestResponse|TestCall */
class RouteTestingTestCall
{
    use ForwardsCalls;

    protected TestCall $testCall;

    protected RouteResolver $routeResolver;

    /** @var array<int, string> */
    protected array $bindingNames = [];

    /** @var array<array{0: string, 1: string}> */
    protected array $assertions = [];

    public function __construct(TestCall $testCall)
    {
        $this->testCall = $testCall;

        $this->routeResolver = new RouteResolver();

        $this->with($this->routeResolver->getFilteredRouteList());
    }

    protected function with(array $routes): self
    {
        $this->testCall->testCaseMethod->datasets = [$routes];

        return $this;
    }

    public function setUp(Closure $closure): static
    {
        $this->testCall->defer($closure);

        return $this;
    }

    /**
     * There is some weird Pest magic going on here... We can't create closures in this class.
     * Instead, just pass the arguments to a different class where we can create closures.
     * It's 3 AM and this took me like 3 days to figure out and I just want to sleep.
     */
    public function bind(string $binding, Closure $closure): self
    {
        RouteTest::bind($binding, $closure);

        $this->bindingNames = array_merge($this->bindingNames, [$binding]);

        $this->with($this->routeResolver->getFilteredRouteList());

        return $this;
    }

    /**
     * @param  string[]|array  $path
     * @return $this
     */
    public function exclude(string|array $path): self
    {
        $this->routeResolver->exceptPaths(Arr::wrap($path));

        $this->with($this->routeResolver->getFilteredRouteList());

        return $this;
    }

    /**
     * @param  array|string[]  $path
     * @return $this
     */
    public function include(array|string $path): self
    {
        $this->routeResolver->paths(Arr::wrap($path));

        $this->with($this->routeResolver->getFilteredRouteList());

        return $this;
    }

    public function __call($method, $parameters): self
    {
        // Assertions cannot be chained on the test call yet until the user is done adding bindings and other Pest test methods.
        // We'll capture assertions and apply them to the TestResponse later (in the __destruct method).
        if (in_array($method, get_class_methods(TestResponse::class))) {
            $this->assertions[] = [$method, $parameters];

            return $this;
        }

        // Make sure Pest's methods (skip, group, etc...) are still callable.
        $this->forwardCallTo($this->testCall, $method, $parameters);

        return $this;
    }

    public function __destruct()
    {
        RouteTest::test($this->testCall, $this->assertions);
    }
}
