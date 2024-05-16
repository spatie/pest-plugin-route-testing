<?php

namespace Spatie\RouteTesting;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Testing\TestResponse;

class RouteTesting
{
    /** @var array<string> */
    protected array $bindings = [];

    /** @var array<string, Route>  */
    protected array $routes = [];

    /** @var array<string> */
    protected array $excludedRoutes = [];

    /** @var array<string, Route>  */
    public array $assertedRoutes = [];

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

    /** @todo get this working */
    public function actingAs(Authenticatable $user, string $guard = null): static
    {
        //test()->actingAs($user, $guard);

        return $this;
    }

    /** @param array<string> $routes */
    public function exclude(array $routes): static
    {
        $this->excludedRoutes = array_merge($this->excludedRoutes, $routes);

        return $this;
    }

    public function test(): static
    {
        $this->assertedRoutes = collect($this->routes)
            ->reject(function (Route $route, string $name) {
                // @todo ignore routes with unfilled bindings
                return in_array($name, $this->excludedRoutes, true);
            })->each(function (Route $route): void {
                $this->assertOkResponse($route, test()->get($route->uri()));
                $this->assertOkResponse($route, test()->getJson($route->uri()));
            })->toArray();

        return $this;
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
            dump($route->uri());
            $response->throwResponse();
        }

        expect($response->getStatusCode())
            ->toBeIn($codes, "Route {$route->uri()} {$route->getActionName()} returned {$response->getStatusCode()}.");
    }
}
