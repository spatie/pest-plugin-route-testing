<?php

use Illuminate\Support\Facades\Route;
use Tests\TestClasses\TestModel;
use Spatie\RouteTesting\RouteTesting;
use function Spatie\RouteTesting\routeTesting;

it('only checks GET endpoints', function () {
    Route::get('/get-endpoint', fn () => '');
    Route::post('/post-endpoint', fn () => '');

    $class = routeTesting()
        ->test();

    expect($class)->toBeInstanceOf(RouteTesting::class);

    expect($class->assertedRoutes)
        ->toHaveCount(1)
        ->toHaveKey('get-endpoint')
        ->not->toHaveKey('post-endpoint');
});

it('can bind a model to a route', function () {
    Route::get('{user}', fn () => '');

    $model = new TestModel();

    $class = routeTesting()
        ->with('user', $model)
        ->test();

    expect($class)->toBeInstanceOf(RouteTesting::class);

    expect($class->assertedRoutes)
        ->toHaveCount(1);

    /** @var \Illuminate\Routing\Route $firstRoute */
    $firstRoute = $class->assertedRoutes['{user}'];

    // @todo is there a way to verify if the binding is set?
});

it('can exclude routes', function () {
    Route::get('/get-endpoint', fn () => '');
    Route::get('/excluded-endpoint', fn () => '');

    $class = routeTesting()
        ->exclude(['excluded-endpoint'])
        ->test();

    expect($class->assertedRoutes)
        ->toHaveCount(1)
        ->toHaveKey('get-endpoint')
        ->not->toHaveKey('post-endpoint');
});
