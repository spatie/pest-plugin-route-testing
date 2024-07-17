<?php

namespace Spatie\RouteTesting;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class RouteResolver
{
    protected ?array $paths = null;

    protected ?array $exceptPaths = null;

    protected array $methods = ['GET'];

    protected bool $exceptRoutesWithMissingBindings = false;

    protected array $bindingNames = [];

    /** @var Collection<int, array{method: string, uri: string}> */
    protected Collection $fullRouteList;

    public function __construct()
    {
        $this->fullRouteList = $this->resolveFullRouteList();
    }

    /** @return Collection<int, array{method: string, uri: string}> */
    protected function resolveFullRouteList(): Collection
    {
        $result = Process::run('php artisan route:list --json --method=GET');

        $routes = json_decode($result->output(), true);

        return collect($routes)->flatMap(
            fn ($route) => Str::of($route['method'])
                ->explode('|')
                ->intersect($this->methods)
                ->map(fn ($method) => ['method' => $method, 'uri' => $route['uri']])
        );
    }

    public function paths(array $paths): self
    {
        $this->paths = array_merge($this->paths ?? [], $paths);

        return $this;
    }

    public function exceptPaths(array $paths): self
    {
        $this->exceptPaths = array_merge($this->exceptPaths ?? [], $paths);

        return $this;
    }

    public function bindingNames(array $bindingNames): self
    {
        $this->bindingNames = $bindingNames;

        return $this;
    }

    public function exceptRoutesWithMissingBindings(): self
    {
        $this->exceptRoutesWithMissingBindings = true;

        return $this;
    }

    public function getFilteredRouteList(): array
    {
        return $this->fullRouteList
            ->filter(function ($route) {
                if ($this->paths) {
                    return collect($this->paths)->contains(fn ($path) => Str::is($path, $route['uri']));
                }

                return true;
            })
            ->filter(function ($route) {
                if ($this->exceptPaths) {
                    return ! collect($this->exceptPaths)->contains(fn ($path) => Str::is($path, $route['uri']));
                }

                return true;
            })
            ->when($this->exceptRoutesWithMissingBindings, function (Collection $routes) {
                return $routes->filter(function ($route) {
                    $uriBindings = $this->getBindingsFromUrl($route['uri']);

                    if (count($uriBindings) === 0) {
                        return true;
                    }

                    return count(array_diff($uriBindings, $this->bindingNames)) === 0;
                });
            })
            ->toArray();
    }

    protected function getBindingsFromUrl(string $uri): array
    {
        $pattern = '/{([^}]*)}/';

        preg_match_all($pattern, $uri, $matches);

        return $matches[1];
    }
}
