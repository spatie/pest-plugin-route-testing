<?php

use Illuminate\Support\Facades\Route;
use Tests\TestClasses\TestModel;
use Spatie\RouteTesting\RouteTesting;
use Tests\TestClasses\TestUser;
use function Spatie\RouteTesting\routeTesting;

it('only checks GET endpoints', function () {
    Route::get('/get-endpoint', fn () => '');
    Route::post('/post-endpoint', fn () => '');

    $class = routeTesting()->assert();

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
        ->assert();

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
        ->assert();

    expect($class->assertedRoutes)
        ->toHaveCount(1)
        ->toHaveKey('get-endpoint')
        ->not->toHaveKey('post-endpoint');
});

it('can act as a user for authenticated routes', function () {
    Route::middleware('auth')->get('/authenticated-endpoint', fn () => '');

    expect(fn () => routeTesting()->assert())
        ->toThrow(\Illuminate\Http\Exceptions\HttpResponseException::class);

    test()->actingAs(new TestUser());
    routeTesting()->assert();
});
