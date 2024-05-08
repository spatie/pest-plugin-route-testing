<?php

use TestClasses\TestModel;
use function Spatie\RouteTesting\routeTesting;

it('can run', function () {
    $authenticatedUser = new \TestClasses\TestUser();

    $dump = routeTesting()
        ->actingAs($authenticatedUser, 'web')
        ->with('user', new TestModel());

    dd($dump);
});
