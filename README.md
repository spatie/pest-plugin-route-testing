# Make sure all routes in your Laravel app are ok

This package provides a simple API to test your application's routes with PestPHP.

Here's a quick example:

```php
use function Spatie\RouteTesting\routeTesting;

it('can access all GET routes of the API', function () {
    routeTesting()
        ->debug()
        ->including(['/api/*'])
        ->excluding(['/api/comments/*'])
        ->bind('post', Post::factory()->create())
        ->assert(function(Response $response) {
            // perform custom assertions
        })
        ->toReturnSuccessfulResponse();
});
```

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-pdf.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-pdf)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/pest-plugin-route-testing
```

## Usage

The easiest way to use this package, is to create a `RoutesTest.php` file in your `tests` directory.

This example checks all GET routes (without route model binding) in your application and ensures they return a 200 HTTP status code or a redirect.

```php
<?php

namespace Tests;

use function Spatie\RouteTesting\routeTesting;

it('can access all GET routes as an admin', function () {
    $user = User::factory()->create(['role' => 'admin']);
    
    test()->actingAs($admin);

    routeTesting()
        ->debug()
        ->toReturnSuccessfulResponse();
});
```

### Debugging

When first using this package, it can be useful to have additional information, such as how many routes are being covered.

```php
routeTesting()
    ->debug()
    ->toReturnSuccessfulResponse();
```

This should output something like the following:

<img src="https://github.com/spatie/pest-plugin-route-testing/assets/10651054/1773dd2a-2f4f-4cea-a6de-ece5ff3547f9" width="300px" />
<img src="https://github.com/spatie/pest-plugin-route-testing/assets/10651054/f3ca6b6b-e783-4f54-ab90-b8521598e7d2" width="300px" />

### Excluding routes

Exclude specific routes from being tested using the excluding method.

```php
routeTesting()
    ->excluding(['api/comments', 'api/posts'])
    ->toReturnSuccessfulResponse();
```

You can also use wildcards to ignore routes:

```php
routeTesting()
    ->excluding(['api/comments/*'])
    ->toReturnSuccessfulResponse();
```

By default, the package ignores certain routes such as `_ignition` and `_debugbar`.

### Including routes

You can only run for specific routes by using the `including` method:

```php
routeTesting()
    ->including(['api/comments', 'api/posts'])
    ->toReturnSuccessfulResponse();
```

You can also use wildcards to inclue routes:

```php
routeTesting()
    ->including(['api/*'])
    ->toReturnSuccessfulResponse();
```

### Combining excludes and includes

In this example, routes like `api/posts/{post}` are excluded, except for `api/posts/{post}/comments`:

```php
routeTesting()
    ->excluding(['api/posts/*'])
    ->including(['api/posts/{post}/comments'])
    ->toReturnSuccessfulResponse();
```

You can also use a wildcard for both.

### Route model binding

Mock route model bindings using the `bind` method:

```php
routeTesting()
    ->bind('user', User::factory()->create())
    ->toReturnSuccessfulResponse();
```

By default, routes with unknown bindings are ignored. The `debug` option can be handy for this!

### Custom assertions

Add custom assertions for specific routes using the `assert` method:

```php
use Illuminate\Testing\TestResponse;

routeTesting()
    ->including('api/*')
    ->assert(function (TestResponse $response) {
        $response->assertStatus(201);
    })
    ->toReturnSuccessfulResponse();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Niels Vanpachtenbeke](https://github.com/nielsvanpach)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
