<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Route;
use Spatie\RouteTesting\RouteResolver;

it('can get a routes', function () {
    setUpRoutes([
        'one',
        'two',
        'three',
    ]);

    $routes = (new RouteResolver)->getFilteredRouteList();

    expect($routes)->toContainRoutes([
        'one',
        'two',
        'three',
    ]);
});

it('can can only get specific paths', function () {
    setUpRoutes([
        'one',
        'one-another',
        'two',
        'three',
    ]);

    $routes = (new RouteResolver)
        ->paths(['one', 'two'])
        ->getFilteredRouteList();

    expect($routes)->toContainRoutes([
        'one',
        'two',
    ]);
});

it('can use a wildcard to get specific paths', function () {
    setUpRoutes([
        'one',
        'one-another',
        'three',
    ]);

    $routes = (new RouteResolver)
        ->paths(['one*'])
        ->getFilteredRouteList();

    expect($routes)->toContainRoutes([
        'one',
        'one-another',
    ]);
});

it('can exclude paths', function () {
    setUpRoutes([
        'one',
        'two',
        'three',
    ]);

    $routes = (new RouteResolver)
        ->exceptPaths(['two'])
        ->getFilteredRouteList();

    expect($routes)->toContainRoutes([
        'one',
        'three',
    ]);
});

it('can exclude paths using a wildcard', function () {
    setUpRoutes([
        'one',
        'one-another',
        'two',
        'three',
    ]);

    $routes = (new RouteResolver)
        ->exceptPaths(['one*'])
        ->getFilteredRouteList();

    expect($routes)->toContainRoutes([
        'two',
        'three',
    ]);
});

it('by default it will also return routes with missing bindings', function () {
    setUpRoutes([
        'home',
        'user/{user}',
    ]);

    $routes = (new RouteResolver)->getFilteredRouteList();

    expect($routes)->toContainRoutes([
        'home',
        'user/{user}',
    ]);
});

it('can ignore routes with missing bindings', function () {
    setUpRoutes([
        'home',
        'user/{user}',
    ]);

    $routes = (new RouteResolver)
        ->exceptRoutesWithMissingBindings()
        ->getFilteredRouteList();

    expect($routes)->toContainRoutes([
        'home',
    ]);
});

it('will not ignore routes whose bindings are not missing', function () {
    setUpRoutes([
        'home',
        'user/{user}',
    ]);

    $routes = (new RouteResolver)
        ->exceptRoutesWithMissingBindings()
        ->bindingNames(['user'])
        ->getFilteredRouteList();

    expect($routes)->toContainRoutes([
        'home',
        'user/{user}',
    ]);
});

function setUpRoutes(array $urls)
{
    foreach ($urls as $url) {
        Route::get($url, function () use ($url) {
            return "Hello {$url}";
        });
    }

    $command = 'route:list --json --method=GET';

    Artisan::call($command);
    $output = Artisan::output();

    Process::fake([
        "php artisan {$command}" => Process::result(output: $output),
    ]);
}
