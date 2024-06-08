<?php

namespace Spatie\RouteTesting;

use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Route;

class RouteTester
{
    public array $assertions = [];

    public function __construct()
    {
    }

    public function assert($s): void
    {
        $this->assertions[] = fn ($r) => $r->assertStatus($s);
    }

    public function test($test, $method, $uri)
    {
        $response = $test->get($uri);

        foreach ($this->assertions as $assertion) {
            $assertion($response);
        }
    }
}
