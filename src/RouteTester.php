<?php

namespace Spatie\RouteTesting;

use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Route;

class RouteTester
{
public \Closure $closure;

    public array $assertions = [];

    public function __construct()
    {
    }

    public function assert($s): void
    {
        $this->assertions[] = $s;
    }

    public function test($test, $method, $uri)
    {
//        $testCall = $this->closure();
        $response = $test->get($uri);

        foreach ($this->assertions as $assertion) {
            $assertion($response);
        }
    }
}
