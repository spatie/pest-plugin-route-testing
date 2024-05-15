<?php

use Illuminate\Support\Facades\Route;
use Tests\TestClasses\TestModel;
use Tests\TestClasses\TestUser;
use Spatie\RouteTesting\RouteTesting;
use function Spatie\RouteTesting\routeTesting;

it('only checks GET endpoints', function () {
    Route::get('/get-endpoint', fn () => '');
    Route::post('/post-endpoint', fn () => '');

    $class = routeTesting()
        ->test();

    expect($class)
        ->toBeInstanceOf(RouteTesting::class)
        ->routes->toHaveCount(1);

    expect($class->routes)
        ->toHaveKey('get-endpoint')
        ->not->toHaveKey('post-endpoint');
});

it('can bind a model to a route', function () {
    Route::get('/{user}', fn () => '');

    $model = new TestModel();

    $class = routeTesting()
        ->with('user', $model)
        ->test();

    expect($class)
        ->toBeInstanceOf(RouteTesting::class)
        ->routes->toHaveCount(1);
});

it('can run with all options', function () {
    $authenticatedUser = new TestUser();

    $dump = routeTesting()
        ->actingAs($authenticatedUser, 'web')
        ->with('user', new TestModel())
        ->exclude(['excluded-route'])
        ->test();

    dd($dump);
});
