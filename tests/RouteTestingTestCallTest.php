<?php

use Pest\PendingCalls\TestCall;
use Pest\TestSuite;
use Spatie\RouteTesting\RouteTestingTestCall;

it('can use any assertion', function (string $assertion, array $expectedAssertions) {
    $routeTestingTestCall = createRouteTestingTestCall();
    $routeTestingTestCall->$assertion();

    expect($routeTestingTestCall)->toHaveProtectedProperty('assertions', $expectedAssertions);
})->with([
    'assertion' => ['assertSuccessful', [['assertSuccessful', []]]],
    'snapshot testing' => ['toMatchSnapshot', [['toMatchSnapshot', []]]],
    'pest\'s method' => ['skip', []],
]);

function createRouteTestingTestCall(): RouteTestingTestCall
{
    $testSuite = new TestSuite('', '');
    $testCall = new TestCall($testSuite, '');

    return new RouteTestingTestCall($testCall);
}
