<?php

declare(strict_types=1);

namespace Spatie\RouteTesting;

use Pest\Plugin;
use PHPUnit\Framework\TestCase;

Plugin::uses(RouteTesting::class);

function routeTesting(string $argument): TestCase
{
    return test()->routeTesting(...func_get_args()); // @phpstan-ignore-line
}
