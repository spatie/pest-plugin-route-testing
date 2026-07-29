<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function defineEnvironment($app): void
    {
        // Laravel 12 registers a `storage/{path}` route for the local disk,
        // which would end up in every resolved route list.
        $app['config']->set('filesystems.disks.local.serve', false);
    }
}
