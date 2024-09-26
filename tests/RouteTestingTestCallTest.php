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
    'expectation' => ['toMatch', [['toMatch', []]]],
    'expectation mixin' => ['toMatchSnapshot', [['toMatchSnapshot', []]]],
    'nonexsitent assertion' => ['nonexistentAssertion', []],
]);

function createRouteTestingTestCall(): RouteTestingTestCall
{
    $testSuite = new TestSuite('', '');
    $testCall = new TestCall($testSuite, '');

    return new RouteTestingTestCall($testCall);
}
