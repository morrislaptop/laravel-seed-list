# Make db:seed interactive

[![Latest Version on Packagist](https://img.shields.io/packagist/v/morrislaptop/laravel-seed-list.svg?style=flat-square)](https://packagist.org/packages/morrislaptop/laravel-seed-list)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/morrislaptop/laravel-seed-list/run-tests?label=tests)](https://github.com/morrislaptop/laravel-seed-list/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/morrislaptop/laravel-seed-list/Check%20&%20fix%20styling?label=code%20style)](https://github.com/morrislaptop/laravel-seed-list/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/morrislaptop/laravel-seed-list.svg?style=flat-square)](https://packagist.org/packages/morrislaptop/laravel-seed-list)

---
Often forget what your seeder classes are called? This package can make `db:seed` interactive so you can see a list and choose which seeders to run (and which seeders will call). 

![screenshot](screenshot.png)

## Installation

You can install the package via composer:

```bash
composer require morrislaptop/laravel-seed-list
```

Configure your default seeder to extend the `LaravelSeedLister` provided by the package:

```php
<?php

namespace Database\Seeders;

use Morrislaptop\LaravelSeedList\LaravelSeedLister;

class DatabaseSeeder extends LaravelSeedLister
{
}
```

## Usage

```bash
php artisan db:seed
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

- [Craig Morris](https://github.com/morrislaptop)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
