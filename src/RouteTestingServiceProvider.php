<?php

namespace Spatie\RouteTesting;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\RouteTesting\Commands\RenderOutputCommand;

class RouteTestingServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('route-testing')
            ->hasCommand(RenderOutputCommand::class)
        ;

    }
}
