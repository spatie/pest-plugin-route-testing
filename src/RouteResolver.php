<?php

namespace Spatie\RouteTesting;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process as SymfonyProcess;

class RouteResolver
{
    /** @var array<int, string>|null */
    protected ?array $paths = null;

    /** @var array<int, string>|null */
    protected ?array $exceptPaths = null;

    /** @var array<int, string> */
    protected array $methods = ['GET'];

    protected bool $exceptRoutesWithMissingBindings = false;

    /** @var array<int, string> */
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
        $command = 'php artisan route:list --json --method=GET';

        try {
            $result = Process::run($command);

            $output = $result->output();
        } catch (Exception) {
            $process = SymfonyProcess::fromShellCommandline($command);

            $process->run();

            $output = $process->getOutput();
        }

        $routes = json_decode($output, true);

        return collect($routes)->flatMap(
            fn (array $route) => Str::of($route['method'])
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

    /**
     * @return array<int, array<int, string>>
     */
    public function getFilteredRouteList(): array
    {
        return $this->fullRouteList
            ->filter(function (array $route) {
                if ($this->paths) {
                    return collect($this->paths)->contains(fn ($path) => Str::is($path, $route['uri']));
                }

                return true;
            })
            ->filter(function (array $route) {
                if ($this->exceptPaths) {
                    return ! collect($this->exceptPaths)->contains(fn ($path) => Str::is($path, $route['uri']));
                }

                return true;
            })
            ->when($this->exceptRoutesWithMissingBindings, function (Collection $routes) {
                return $routes->filter(function (array $route) {
                    $uriBindings = $this->getBindingsFromUrl($route['uri']);

                    if (count($uriBindings) === 0) {
                        return true;
                    }

                    return count(array_diff($uriBindings, $this->bindingNames)) === 0;
                });
            })
            ->map(fn (array $route) => array_values($route))
            ->toArray();
    }

    protected function getBindingsFromUrl(string $uri): array
    {
        $pattern = '/{([^}]*)}/';

        preg_match_all($pattern, $uri, $matches);

        return $matches[1];
    }
}
