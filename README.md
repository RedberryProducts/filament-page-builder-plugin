# This is my package page-builder-plugin

[![Latest Version on Packagist](https://img.shields.io/packagist/v/redberryproducts/page-builder-plugin.svg?style=flat-square)](https://packagist.org/packages/redberryproducts/page-builder-plugin)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/redberryproducts/page-builder-plugin/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/redberryproducts/page-builder-plugin/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/redberryproducts/page-builder-plugin/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/redberryproducts/page-builder-plugin/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/redberryproducts/page-builder-plugin.svg?style=flat-square)](https://packagist.org/packages/redberryproducts/page-builder-plugin)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require redberryproducts/page-builder-plugin
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="page-builder-plugin-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="page-builder-plugin-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="page-builder-plugin-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$pageBuilderPlugin = new RedberryProducts\PageBuilderPlugin();
echo $pageBuilderPlugin->echoPhrase('Hello, RedberryProducts!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [RedberryProducts](https://github.com/RedberryProducts)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
