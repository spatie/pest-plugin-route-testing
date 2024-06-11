<?php

namespace Spatie\RouteTesting;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Spatie\RouteTesting\Commands\RenderOutputCommand;

class RouteTestingOld
{
    /** @var array<string, Route> */
    public array $assertedRoutes = [];

    /** @var array<string> */
    public array $ignoredBindings = [];

    /** @var array<string, Route> */
    protected array $routes = [];

    /** @var array<string> */
    protected array $excludedRoutes = [];

    /** @var array<string> */
    protected array $includedRoutes = [];

    /** @var array<string> */
    protected array $bindings = [];

    /** @var array<string> */
    protected array $defaultIgnoredRoutes = [
        '_ignition',
        '_debugbar',
        'horizon*',
        'pulse*',
        'sanctum*',
    ];

    /** @var array<callable> */
    protected array $customAssertions = [];

    protected bool $renderDebugInfo = false;

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

    public function debug(bool $display = true): static
    {
        $this->renderDebugInfo = $display;

        return $this;
    }

    public function toReturnSuccessfulResponse(): static
    {
        $this->assertedRoutes = collect($this->routesToAssert())
            ->each(function (Route $route): void {
                $this->assertOkResponse($route, test()->get($route->uri()));

                if ($this->customAssertions) {
                    foreach ($this->customAssertions as $assertion) {
                        $assertion(test()->get($route->uri()));
                    }
                }
            })->toArray();

        if ($this->renderDebugInfo) {
            $this->renderOutput();
        }

        return $this;
    }

    /** @return array<Route> */
    protected function routesToAssert(): array
    {
        return collect($this->routes)
            ->reject(fn (Route $route): bool => $this->shouldIgnoreRoute($route))
            ->toArray();
    }

    protected function shouldIgnoreRoute(Route $route): bool
    {
        if ($this->isIgnored($route->uri())) {
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
            if (Str::is($excludedRoute, $name)) {
                return true;
            }
        }

        return false;
    }

    protected function isIncluded(string $name): bool
    {
        foreach ($this->includedRoutes as $includedRoute) {
            if (Str::is($includedRoute, $name)) {
                return true;
            }
        }

        return false;
    }

    protected function isIgnored(string $name): bool
    {
        foreach ($this->defaultIgnoredRoutes as $ignoredRoute) {
            if (Str::is($ignoredRoute, $name)) {
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

        $this->ignoredBindings = array_merge($this->ignoredBindings, [$bindingName]);

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

    protected function renderOutput(): void
    {
        $countAsserted = count($this->assertedRoutes);
        $countTotal = count($this->assertedRoutes) + count($this->ignoredBindings);

        Artisan::call(RenderOutputCommand::class, [
            'asserted' => $countAsserted,
            'total' => $countTotal,
            'ignored' => implode(',', $this->ignoredBindings),
        ]);
    }
}
