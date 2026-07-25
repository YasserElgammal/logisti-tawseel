# Logisti Tawseel Laravel Package

Laravel package for integrating with the Logisti / Tawseel delivery service provider APIs.

> **API version:** This package follows the *Integration Guide for Delivery Service Providers*, V1.27, dated 30/06/2026.

## Features

- Laravel auto-discovery service provider and `Logisti` facade.
- Fluent builders for drivers, orders, contact information, and order actions.
- Raw gateway methods when you need to send an exact Tawseel payload.
- Lookup API gateway for reading Tawseel lookup values directly.
- Database logging for API requests and responses.
- Typed response wrapper and package exceptions.
- Testbench/PHPUnit test suite for package development.

## Requirements

- PHP `^8.0`
- Laravel components `^8.0|^9.0|^10.0|^11.0|^12.0|^13.0`
- Composer

## Installation

Install the package in a Laravel application:

```bash
composer require yasser-elgammal/logisti-tawseel
```

Laravel package auto-discovery registers the service provider and facade. If auto-discovery is disabled, register the provider manually:

```php
'providers' => [
    YasserElgammal\LogistiTawseel\LogistiServiceProvider::class,
],
```

Publish the config file:

```bash
php artisan vendor:publish --tag=logisti-config
```

Run the package migrations:

```bash
php artisan migrate
```

The migration creates `logisti_requests` for optional request/response logging.

## Configuration

Add the credentials and runtime options to your application `.env` file:

```env
LOGISTI_ENABLED=true
LOGISTI_ENVIRONMENT=staging

LOGISTI_STAGING_URL=https://tawseel-stg.api.elm.sa
LOGISTI_PRODUCTION_URL=https://tawseel.api.elm.sa

LOGISTI_APP_ID=
LOGISTI_APP_KEY=

LOGISTI_TIMEOUT=30
LOGISTI_RETRY_TIMES=2
LOGISTI_RETRY_SLEEP=500

LOGISTI_LOG_REQUESTS=true
LOGISTI_THROW_EXCEPTIONS=true
```

`LOGISTI_ENVIRONMENT` selects a URL from `config/logisti.php`. Use `staging` while testing and `production` when you are ready to send live requests.

If your application caches config, clear or rebuild it after changing environment values:

```bash
php artisan config:clear
php artisan config:cache
```

## Basic Usage

Use the facade from application code:

```php
use YasserElgammal\LogistiTawseel\Facades\Logisti;

$response = Logisti::lookups()->regions();

if ($response->successful()) {
    $regions = $response->data();
}
```

You may also inject the manager:

```php
use YasserElgammal\LogistiTawseel\LogistiManager;

public function __construct(private LogistiManager $logisti)
{
}
```

## Drivers

Create a driver:

```php
$response = Logisti::drivers()
    ->create()
    ->identityTypeId($identityTypeId)
    ->idNumber('1016990911')
    ->dateOfBirth(14190408)
    ->registrationDate(now())
    ->mobile('0555555555')
    ->regionId($regionId)
    ->cityId($cityId)
    ->vehicleSequenceNumber('277525810')
    ->send();
```

Edit a driver:

```php
$response = Logisti::drivers()
    ->edit()
    ->refrenceCode($referenceCode)
    ->identityTypeId($identityTypeId)
    ->idNumber('1016990911')
    ->dateOfBirth(14190408)
    ->registrationDate(now())
    ->mobile('0555555555')
    ->regionId($regionId)
    ->cityId($cityId)
    ->vehicleSequenceNumber('277525810')
    ->send();
```

Other driver operations:

```php
Logisti::drivers()->get('1016990911');
Logisti::drivers()->deactivate('1016990911');
```

Driver raw methods:

```php
Logisti::drivers()->createRaw($payload);
Logisti::drivers()->editRaw($payload);
```

## Orders

Create an order:

```php
$response = Logisti::orders()
    ->create()
    ->orderNumber('ORD-1001')
    ->authorityId($authorityId)
    ->deliveryTime(now()->addHour())
    ->regionId($regionId)
    ->cityId($cityId)
    ->deliveryCoordinates('24.7842', '46.6453')
    ->storeName('Test Store')
    ->storeCoordinates('24.751433', '46.740517')
    ->categoryId($categoryId)
    ->orderDate(now())
    ->recipientMobileNumber('966555555555')
    ->send();
```

Order actions:

```php
Logisti::orders()->get($referenceCode);

Logisti::orders()
    ->accept()
    ->referenceCode($referenceCode)
    ->acceptanceDateTime(now())
    ->send();

Logisti::orders()
    ->reject()
    ->referenceCode($referenceCode)
    ->send();

Logisti::orders()
    ->assignDriver()
    ->referenceCode($referenceCode)
    ->idNumber('1016990911')
    ->send();

Logisti::orders()
    ->editDeliveryAddress()
    ->referenceCode($referenceCode)
    ->regionId($regionId)
    ->cityId($cityId)
    ->deliveryCoordinates('24.7842', '46.6453')
    ->storeCoordinates('24.751433', '46.740517')
    ->send();

Logisti::orders()
    ->cancel()
    ->referenceCode($referenceCode)
    ->cancellationReasonId($reasonId)
    ->send();
```

Execute an order:

```php
$response = Logisti::orders()
    ->execute()
    ->referenceCode($referenceCode)
    ->executionTime(now())
    ->paymentMethodId($paymentMethodId)
    ->amounts(150.0, 30.5, 20.0)
    ->send();
```

`amounts($priceWithoutDelivery, $deliveryPrice, $driverIncome)` automatically sets `price` to `priceWithoutDelivery + deliveryPrice`.

Order raw methods:

```php
Logisti::orders()->createRaw($payload);
Logisti::orders()->acceptRaw($payload);
Logisti::orders()->rejectRaw($payload);
Logisti::orders()->assignDriverRaw($payload);
Logisti::orders()->editDeliveryAddressRaw($payload);
Logisti::orders()->executeRaw($payload);
Logisti::orders()->cancelRaw($payload);
```

## Contact Info

Create or update application contact information:

```php
$response = Logisti::contactInfo()
    ->createOrUpdate()
    ->responsibleName('Operations')
    ->responsibleEmail('ops@example.com')
    ->responsibleMobileNumber('966555555555')
    ->technicalName('Tech Support')
    ->technicalEmail('tech@example.com')
    ->technicalMobileNumber('966566666666')
    ->send();
```

Other contact info operations:

```php
Logisti::contactInfo()->get();
Logisti::contactInfo()->createOrUpdateRaw($payload);
```

## Lookups

Available lookup methods:

```php
Logisti::lookups()->authorities();
Logisti::lookups()->cancellationReasons();
Logisti::lookups()->regions();
Logisti::lookups()->categories();
Logisti::lookups()->identityTypes();
Logisti::lookups()->paymentMethods();
Logisti::lookups()->carTypes();
Logisti::lookups()->countries();
Logisti::lookups()->cities($regionId);
```

## Responses

All gateway calls return `YasserElgammal\LogistiTawseel\Http\LogistiResponse`, unless exceptions are enabled and the request fails.

```php
$response->successful();
$response->failed();
$response->data();
$response->data('referenceCode');
$response->errorCodes();
$response->body();
$response->statusCode();
$response->raw();
```

`data()` returns the API `data` field when present. Otherwise it returns the decoded response body.

## Validation

Fluent builders validate payloads before sending. For example:

- Driver `idNumber` must be 10 digits and start with `1` or `2`.
- Driver `mobile` must be a 10 digit Saudi local mobile number beginning with `05`.
- Order recipient and contact mobile numbers must use the `9665xxxxxxxx` format.
- Execute order `price` must equal `priceWithoutDelivery + deliveryPrice`.

Validation failures throw `LogistiValidationException`.

## Error Handling

When `LOGISTI_THROW_EXCEPTIONS=true`:

- Failed API responses throw `LogistiApiException`.
- Connection failures throw `LogistiConnectionException`.
- Builder validation failures throw `LogistiValidationException`.

Example:

```php
use YasserElgammal\LogistiTawseel\Exceptions\LogistiApiException;
use YasserElgammal\LogistiTawseel\Support\ErrorCodeMapper;

try {
    $response = Logisti::orders()->get($referenceCode);
} catch (LogistiApiException $exception) {
    $messages = ErrorCodeMapper::messages(
        $exception->response->errorCodes(),
        'en'
    );
}
```

When `LOGISTI_THROW_EXCEPTIONS=false`, failed calls return a `LogistiResponse` object. Check `$response->failed()` and `$response->errorCodes()`.

## Request Logging

When `LOGISTI_LOG_REQUESTS=true`, the package writes each attempted API request to `logisti_requests`.

Logged fields include endpoint, method, payload, response body, HTTP status, error codes, success flag, duration, and exception message. Logging is wrapped in a try/catch so logging failures do not break API calls.

Disable logging when you do not want request payloads stored:

```env
LOGISTI_LOG_REQUESTS=false
```

## Package Structure

```text
config/logisti.php                         Package config
database/migrations/                      Request logging table
src/LogistiServiceProvider.php            Laravel registration
src/LogistiManager.php                    Main manager behind the facade
src/Facades/Logisti.php                   Laravel facade
src/Gateways/                             Driver, order, lookup, contact gateways
src/Builders/                             Fluent payload builders
src/Http/LogistiClient.php                HTTP transport, retry, logging
src/Http/LogistiResponse.php              Response wrapper
src/Models/                               Eloquent model for request logs
src/Support/                              Helper classes
tests/                                    PHPUnit/Testbench tests
```

## Notes About Tawseel Field Names

The Tawseel integration guide includes some typo field names. This package preserves them where needed for API compatibility:

- `refrenceCode`
- `storetName`
- `cancelationReasonId`

`storeName()` is provided as a readable alias and maps to the documented `storetName` payload key.

Cancel Order uses `referenceCode`. The readable `cancellationReasonId()` method serializes the external key as the documented `cancelationReasonId`.

## License

This package is open-sourced software licensed under the [MIT License](LICENSE).

