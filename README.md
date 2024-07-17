# Make sure all routes in your Laravel app are ok

In a typical Laravel application there are many pages that can be accessed by users. It's easy to forget to test all of them. This package makes it easy to test all GET routes in your application.

Here's a quick example:

```php
use function Spatie\RouteTesting\routeTesting;

routeTesting('all GET routes')
   ->assertSuccessful();
```

This will test all GET routes in your application and ensure they return a 200 HTTP status code. Here's what the output looks like when you run this test in a small app.

// INSERT IMAGE

Instead of `assertSuccessful()` you can use any assertion that is available in Laravel's `TestResponse` class, such as `assertRedirect()`, `assertNotFound()`, `assertForbidden()`, etc.

You can also test specific routes:

```php
use function Spatie\RouteTesting\routeTesting;

routeTesting('all blog routes')
    ->include('blog*')
    ->assertSuccessful();
```

If you there are routes that have route model bindings, the package will skip the test for those routes. Let's assume you have a route defined as  `user/{user}`. Here's what the output looks like when you run the test.

// INSERT IMAGE

If you want to test a route with a route model binding, you can provide the model using the `bind` method.

```php
use function Spatie\RouteTesting\routeTesting;
use App\Models\User;

routeTesting('all blog routes')
    ->bind('user', User::factory()->create())
    ->assertSuccessful();
```

When you run the test now, the package will use the provided model to test the route.

// INSERT IMAGE

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

You can use the `routeTesting` function to test all routes in your application.

```php
use function Spatie\RouteTesting\routeTesting;

routeTesting('all routes')
    ->assertSuccessful();
```

This will test all GET routes in your application and ensure they return a 200 HTTP status code. Here's what the output looks like when you run this test in a small app.

// INSERT IMAGE

Instead of `assertSuccessful()` you can use any assertion that is available in Laravel's `TestResponse` class, such as `assertRedirect()`, `assertNotFound()`, `assertForbidden()`, etc.

### Testing specific routes

// Coming soon

### Excluding routes

// Coming soon

### Binding route model bindings

// Coming soon

### Ignoring routes with missing route model bindings

// Coming soon

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
