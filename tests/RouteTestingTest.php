<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Route;
use Spatie\RouteTesting\RouteResolver;

it('can get a route', function () {
    setUpRoutes([
        'home',
    ]);

    $routes = (new RouteResolver())->getFilteredRouteList();

    expect($routes)->toContainRoutes([
        'home',
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
