# Create eloquent datatables

[![Latest Version on Packagist](https://img.shields.io/packagist/v/designbycode/eloquent-datatable.svg?style=flat-square)](https://packagist.org/packages/designbycode/eloquent-datatable)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/designbycode/eloquent-datatable/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/designbycode/eloquent-datatable/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/designbycode/eloquent-datatable/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/designbycode/eloquent-datatable/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/designbycode/eloquent-datatable.svg?style=flat-square)](https://packagist.org/packages/designbycode/eloquent-datatable)

An Eloquent way to build datatables.


## Installation

You can install the package via composer:

```bash
composer require designbycode/eloquent-datatable
```

## Usage
To create a datatable controller you have to run the following command. This will generate a new controller with all the needed files.
```bash
php artisan datatable:create PostsDatatableController
```
The artisan command comes with 2 optional parameters.
The `--model` parameter defines which model to use for the datatable. The `--middleware` parameter defines which middleware to use for the datatable.
```bash
php artisan datatable:create PostsDatatableController --model=Post --middleware=auth,auth.admin
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

- [Claude Myburgh](https://github.com/designbycode)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
