<?php

declare(strict_types=1);

namespace Pest\PluginName;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
trait RouteTesting
{
    public function routeTesting(string $name): TestCase
    {
        expect($name)->toBeString();

        return $this;
    }
}
