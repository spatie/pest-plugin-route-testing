<?php

namespace Spatie\RouteTesting;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

class RouteTesting
{
    protected array $bindings = [];

    protected array $routes = [];

    protected array $excludedRoutes = [];

    protected array $routesWithUnfilledBindings = [];

    public function __construct()
    {
        $this->routes = RouteFacade::getRoutes()->getRoutesByMethod()['GET'];
    }

    public function with(string $binding, mixed $modelOrCollection): static
    {
        RouteFacade::bind($binding, fn () => $modelOrCollection);

        $this->bindings = array_merge($this->bindings, [$binding]);

        return $this;
    }

    public function actingAs(Authenticatable $user, string $guard = null): static
    {
        test()->actingAs($user, $guard);

        return $this;
    }

    public function exclude(array $routes): static
    {
        $this->excludedRoutes = array_merge($this->excludedRoutes, $routes);

        return $this;
    }

    public function test(): static
    {
        collect($this->routes)
            ->reject(fn ($route) => in_array($route->uri, $this->excludedRoutes))
            ->toArray();

        return $this;
    }

    protected function ignoreRoutesWithUnfilledBindings(Route $route)
    {
    }
}
