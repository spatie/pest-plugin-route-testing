<?php

namespace Spatie\RouteTesting;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Route;

class RouteTesting
{
    public function with(string $binding, mixed $modelOrCollection): static
    {
        Route::bind($binding, fn () => $modelOrCollection);

        return $this;
    }

    public function actingAs(Authenticatable $user, string $guard = null): static
    {
        test()->actingAs($user, $guard);

        return $this;
    }
}
