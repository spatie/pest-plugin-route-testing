# Make sure all routes in your Laravel app are ok

This package provides a simple way to test your application's routes with PestPHP.

Here's a quick example:

```php
use function Spatie\RouteTesting\routeTesting;

routeTesting()
    ->actingAs(User::factory()->create(), 'api')
    ->with('post', Post::factory()->create())
    ->exclude(['/api/comments/*'])
    ->test()
    ->assertHasJsonApiPagination()
    ->assertOk()
     ->assert(function(Response $response) {
        // Custom assertions
    });;
```

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-pdf.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-pdf)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Documentation

All documentation is available [on our documentation site](https://spatie.be/docs/pest-plugin-route-testing).

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
