# Fingerprint Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/erayaydin/fingerprint-laravel.svg?style=flat-square)](https://packagist.org/packages/erayaydin/fingerprint-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/erayaydin/fingerprint-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/erayaydin/fingerprint-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/erayaydin/fingerprint-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/erayaydin/fingerprint-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/erayaydin/fingerprint-laravel.svg?style=flat-square)](https://packagist.org/packages/erayaydin/fingerprint-laravel)
[![Test Coverage](https://raw.githubusercontent.com/erayaydin/fingerprint-laravel/main/badge-coverage.svg)](https://packagist.org/packages/erayaydin/fingerprint-laravel)

Fingerprint Laravel is a package for integrating Fingerprint Server API into your Laravel application with [PHP SDK for Fingerprint Pro Server API](https://github.com/fingerprintjs/fingerprint-pro-server-api-php-sdk).
It provides HTTP middlewares to block bots, VPNs, Tor, and more based on Fingerprint Server API event response.

## Requirements

- **Laravel 11**
- **PHP ^8.2**

## Features

- Customizable implementations with `fingerprint` config file.
- Injectable `Event` and `Fingerprint` classes. `Event` data class will auto-bind when a request with `requestId`
received.
- `Fingerprint` class provides a fluent interface to interact with the Fingerprint Server API.
- Ready to use HTTP middlewares to block bots, VPNs, Tor, and more based on Fingerprint Server API event response.

## Installation

You can install the package via Composer:

```shell
$ composer require erayaydin/fingerprint-laravel
```

Check installation with about command.

## Configuration

Publish the configuration file:

```shell
$ php artisan vendor:publish --tag=fingerprint-config
```

This will create a config/fingerprint.php file where you can set configurations.

> By default, the package will use the `FINGERPRINT_PRO_SECRET_API_KEY` and `FINGERPRINT_REGION` environment variables.
> You should specify these values in your `.env` file. You can change the environment variable names in the
> configuration file after publishing it.

### Configuration Options

- **api_secret**: Your Fingerprint Server API key.
- **region**: The region of the Fingerprint Server API. Available options: `eu`/`europe`, `ap`/`asia`, `global`.
- **middleware**
  - **bot_block**: Blocks good and/or bad bots.
  - **vpn_block**: Blocks request if user is using a VPN.
  - **tor_block**: Blocks tor network users.
  - **min_confidence**: Minimum required confidence score. It should be in range of 0.0 to 1.0. If it's null, it will 
not check the confidence score.
  - **incognito_block**: Blocks users who are using incognito mode.
  - **max_elapsed_time**: Maximum elapsed time between the request and the event identification.

## Usage

### Middlewares

The package provides several middleware to block different types of traffics:

- BlockBotsMiddleware (`fingerprint.bots`)
- BlockIncognitoMiddleware (`fingerprint.incognito`)
- BlockOldIdentificationMiddleware (`fingerprint.old-identification`)
- BlockTorMiddleware (`fingerprint.tor`)
- BlockVPNMiddleware (`fingerprint.vpn`)
- MinConfidenceScoreMiddleware (`fingerprint.confidence`)

You can register these middleware in your `bootstrap/app.php` file or use the provided middleware group `fingerprint` in
routes or controllers.

To use the middleware group in a route:

```php
Route::middleware(['fingerprint'])->group(function () {
    // Request is valid!
});
```

Or you can use specific middlewares:

```php
Route::middleware(['fingerprint.incognito', 'fingerprint.vpn'])->group(function () {
    // User is not in incognito mode and not using VPN!
});
```

### Event Data Access

Use dependency injection to access the `Event` data class in your controller or middleware:

```php
class ExampleController extends Controller
{
    public function store(Event $event)
    {
        ray($event->identification, $event->botD, $event->isTor, $event->isVPN);
    }
}
```

### Fingerprint Server API Access

Use dependency injection to access the `Fingerprint` class in your controller or middleware:

```php
class ExampleController extends Controller
{
    public function store(Fingerprint $fingerprint)
    {
        ray($fingerprint->getEvent('requestId'));
    }
}
```

### About Command Support

The package integrates with Laravel's `AboutCommand` to provide information about the fingerprinting configuration.
This is registered automatically.

```shell
$ php artisan about
...
  Fingerprint Laravel .........................................................
  API Key ...................................................... Not Configured
  Bot Block ................................................ Enabled (Bad Bots)
  Incognito Block ..................................................... Enabled
  Min. Confidence ......................................................... 0.8
  Old Identification ............................................... 10 seconds
  Region ................................................................... eu
  TOR Block ............................................. Enabled (if signaled)
  VPN Block ........................................................... Enabled
  Version ................................................ 1.0.0+no-version-set
```

## Testing

You can run the tests with:

```shell
composer test
```

## Roadmap

- Increase code coverage to ~95%.
- Add config option to change `requestId` key.
- Add IP block middleware.
- Add auto visitor store support with `Visitor` model and migrations.
- Add `HasVisitorId` trait to use with custom Eloquent models.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
