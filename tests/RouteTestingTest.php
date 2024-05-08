<?php

use TestClasses\TestModel;
use function Spatie\RouteTesting\routeTesting;

it('can run', function () {
    $dump = routeTesting()
        ->test();

    dd($dump);
});

it('can run with all options', function () {
    $authenticatedUser = new \TestClasses\TestUser();

    $dump = routeTesting()
        ->actingAs($authenticatedUser, 'web')
        ->with('user', new TestModel())
        ->exclude(['excluded-route'])
        ->test();

    dd($dump);
});
