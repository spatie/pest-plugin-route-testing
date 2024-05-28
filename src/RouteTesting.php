<?php

namespace Spatie\RouteTesting;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

class RouteTesting
{
    /** @var array<string, Route>  */
    public array $assertedRoutes = [];

    /** @var array<string> */
    public array $ignoredRoutes = [];

    /** @var array<string, Route>  */
    protected array $routes = [];

    /** @var array<string> */
    protected array $excludedRoutes = [];

    /** @var array<string> */
    protected array $includedRoutes = [];

    /** @var array<string> */
    protected array $bindings = [];

    /** @var array<string>  */
    protected array $defaultIgnoredRoutes = [
        '_ignition',
        '_debugbar',
    ];

    /** @var array<callable> */
    protected array $customAssertions = [];

    public function __construct()
    {
        $this->routes = RouteFacade::getRoutes()->getRoutesByMethod()['GET'];
    }

    public function bind(string $binding, mixed $modelOrCollection): static
    {
        RouteFacade::bind($binding, fn () => $modelOrCollection);

        $this->bindings = array_merge($this->bindings, [$binding]);

        return $this;
    }

    /** @param array<string> $routes */
    public function excluding(array $routes): static
    {
        $this->excludedRoutes = array_merge($this->excludedRoutes, $routes);

        return $this;
    }

    /** @param array<string> $routes */
    public function including(array $routes): static
    {
        $this->includedRoutes = array_merge($this->includedRoutes, $routes);

        return $this;
    }

    public function assert(callable $closure): static
    {
        $this->customAssertions = array_merge($this->customAssertions, [$closure]);

        return $this;
    }

    public function toReturnSuccessfulResponse(): static
    {
        $this->assertedRoutes = collect($this->routesToAssert())
            ->each(function (Route $route): void {
                if ($this->customAssertions) {
                    foreach ($this->customAssertions as $assertion) {
                        $assertion(test()->get($route->uri()));
                    }
                }

                $this->assertOkResponse($route, test()->get($route->uri()));
                $this->assertOkResponse($route, test()->getJson($route->uri()));
            })->toArray();

        $countAsserted = count($this->assertedRoutes);
        $countTotal = count($this->assertedRoutes) + count($this->ignoredRoutes);

        if ($countAsserted < $countTotal) {
            dump("Tested {$countAsserted} out of {$countTotal} routes.");
        }

        return $this;
    }

    /** @return array<Route> */
    protected function routesToAssert(): array
    {
        return collect($this->routes)
            ->reject(fn (Route $route) => $this->shouldIgnoreRoute($route))
            ->toArray();
    }

    protected function shouldIgnoreRoute(Route $route): bool
    {
        if (Str::startsWith($route->uri(), $this->defaultIgnoredRoutes)) {
            return true;
        }

        if ($this->includedRoutes && ! $this->isIncluded($route->uri())) {
            return true;
        }

        if ($this->isExcluded($route->uri())) {
            return true;
        }

        if ($this->hasUnknownBindings($route)) {
            return true;
        }

        return false;
    }

    protected function isExcluded(string $name): bool
    {
        foreach ($this->excludedRoutes as $excludedRoute) {
            if(Str::is($excludedRoute, $name)) {
                return true;
            }
        }

        return false;
    }

    protected function isIncluded(string $name): bool
    {
        foreach ($this->includedRoutes as $includedRoute) {
            if(Str::is($includedRoute, $name)) {
                return true;
            }
        }

        return false;
    }

    protected function hasUnknownBindings(Route $route): bool
    {
        if (! str_contains($route->uri, '{')) {
            return false;
        }

        $bindingName = substr($route->uri, strpos($route->uri, '{') + 1, strpos($route->uri, '}') - strpos($route->uri, '{') - 1);

        if (in_array($bindingName, $this->bindings, true)) {
            return false;
        }

        $this->ignoredRoutes = array_merge($this->ignoredRoutes, [$route->uri]);

        return true;
    }

    protected function assertOkResponse(Route $route, TestResponse $response): void
    {
        if ($response->isRedirect()) {
            $response->assertRedirect();

            return;
        }

        if (property_exists($response->baseResponse, 'exception')
            && str_starts_with($response->exception?->getMessage(), 'Call to undefined method ')) {
            return;
        }

        $codes = [200];

        if ($response->getStatusCode() === 500) {
            $response->throwResponse();
        }

        expect($response->getStatusCode())
            ->toBeIn($codes, "Route {$route->uri()} {$route->getActionName()} returned {$response->getStatusCode()}.");
    }
}
