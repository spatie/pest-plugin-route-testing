<?php

namespace Spatie\RouteTesting;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
//use Illuminate\Routing\Router as RouteFacade;
use Illuminate\Testing\TestResponse;

final class RouteTesting
{
    private array $bindings = [];

    private array $routes = [];

    private array $excludedRoutes = [];
    private const ROUTES_WITH_UNFILLED_BINDINGS = [];
    /**
     * @var int[]
     */
    private const CODES = [200];

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
        // @todo ignore routes with unfilled bindings

        collect($this->routes)
            ->reject(fn ($route): bool => in_array($route->uri, $this->excludedRoutes))
            ->each(function (Route $route): void {
                $this->assertOkResponse($route, test()->get($route->uri()));
                $this->assertOkResponse($route, test()->getJson($route->uri()));
            });

        return $this;
    }

    private function assertOkResponse(Route $route, TestResponse $response): void
    {
        if ($response->isRedirect()) {
            $response->assertRedirect();

            return;
        }

        if (property_exists($response->baseResponse, 'exception')
            && str_starts_with((string) optional($response->exception)->getMessage(), 'Call to undefined method ')) {
            return;
        }

        if ($response->getStatusCode() === 500) {
            dump($route->uri());
            $response->throwResponse();
        }

        expect($response->getStatusCode())
            ->toBeIn(self::CODES, "Route {$route->uri()} {$route->getActionName()} returned {$response->getStatusCode()}.");

    }
}
