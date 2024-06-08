<?php

namespace Spatie\RouteTesting;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class RouteResolver
{
    protected ?array $paths = null;

    protected ?array $exceptPaths = null;

    protected array $methods = ['GET'];

    /** @var Collection<int, array{method: string, uri: string}> */
    protected Collection $fullRouteList;

    public function __construct()
    {
        $this->fullRouteList = $this->resolveFullRouteList();
    }

    /** @return Collection<int, array{method: string, uri: string}> */
    protected function resolveFullRouteList(): Collection
    {
        $process = Process::fromShellCommandline('php artisan route:list --json --method=GET');
        $process->run();

        $routes = json_decode($process->getOutput(), true);

        return collect($routes)->flatMap(
            fn($route) => Str::of($route['method'])
            ->explode('|')
            ->intersect($this->methods)
            ->map(fn($method) => ['method' => $method, 'uri' => $route['uri']])
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

    public function getFilteredRouteList(): array
    {
        return $this->fullRouteList
            ->filter(function ($route) {
                if ($this->paths) {
                    return collect($this->paths)->contains(fn($path) => Str::is($path, $route['uri']));
                }

                return true;
            })
            ->filter(function ($route) {
                if ($this->exceptPaths) {
                    return ! collect($this->exceptPaths)->contains(fn($path) => Str::is($path, $route['uri']));
                }

                return true;
            })
            ->toArray();
    }
}
