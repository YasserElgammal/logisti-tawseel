<?php

namespace YasserElgammal\LogistiTawseel\Tests\Unit;

use Illuminate\Support\Facades\Http;
use YasserElgammal\LogistiTawseel\Http\LogistiClient;
use YasserElgammal\LogistiTawseel\Http\LogistiResponse;
use YasserElgammal\LogistiTawseel\Support\ErrorCodeMapper;
use YasserElgammal\LogistiTawseel\Tests\TestCase;

class ClientAndGatewayTest extends TestCase
{
    public function test_client_sends_auth_headers_and_uses_staging_url(): void
    {
        config()->set('logisti.environment', 'staging');
        config()->set('logisti.urls.staging', 'https://staging.example.test');
        Http::fake(['*' => Http::response(['status' => true, 'data' => ['ok' => true]], 200)]);

        app(LogistiClient::class)->post('/external/api/driver/create', ['a' => 'b']);

        Http::assertSent(fn ($request) => $request->url() === 'https://staging.example.test/external/api/driver/create'
            && $request->hasHeader('app-id', 'app-id')
            && $request->hasHeader('app-key', 'app-key')
            && $request['a'] === 'b');
    }

    public function test_client_uses_production_url_and_parses_failed_response(): void
    {
        config()->set('logisti.environment', 'production');
        config()->set('logisti.urls.production', 'https://production.example.test');
        Http::fake(['*' => Http::response(['status' => false, 'errorCodes' => [47]], 422)]);

        $response = app(LogistiClient::class)->get('/external/api/driver/1');

        $this->assertTrue($response->failed());
        
        $this->assertSame([47], $response->errorCodes());
        Http::assertSent(fn ($request) => $request->url() === 'https://production.example.test/external/api/driver/1');
    }

    public function test_create_driver_sends_documented_request_shape(): void
    {
        config()->set('logisti.urls.staging', 'https://staging.example.test');
        Http::fake(['*' => Http::response(['status' => true], 200)]);

        \YasserElgammal\LogistiTawseel\Facades\Logisti::drivers()->create()
            ->identityTypeId('NV25GlPuOnQ=')
            ->idNumber('1016990911')
            ->dateOfBirth(19900419)
            ->registrationDate('2020-04-02T17:41:59.277Z')
            ->mobile('0555555555')
            ->regionId('NV25GlPuOnQ=')
            ->cityId('NV25GlPuOnQ=')
            ->vehicleSequenceNumber('123456789')
            ->send();

        Http::assertSent(fn ($request) =>
            $request->url() === 'https://staging.example.test/external/api/driver/create'
            && $request->method() === 'POST'
            && $request->hasHeader('Content-Type', 'application/json')
            && $request->hasHeader('app-id', 'app-id')
            && $request->hasHeader('app-key', 'app-key')
            && $request->data() === [
                'driver' => [
                    'identityTypeId' => 'NV25GlPuOnQ=',
                    'idNumber' => '1016990911',
                    'dateOfBirth' => 19900419,
                    'registrationDate' => '2020-04-02T17:41:59.277Z',
                    'mobile' => '0555555555',
                    'regionId' => 'NV25GlPuOnQ=',
                    'cityId' => 'NV25GlPuOnQ=',
                    'vehicleSequenceNumber' => '123456789',
                ],
            ]);
    }

    public function test_v127_wrapped_and_flat_order_payloads(): void
    {
        config()->set('logisti.urls.staging', 'https://staging.example.test');
        Http::fake(['*' => Http::response(['status' => true, 'errorCodes' => [0]], 200)]);
        $orders = \YasserElgammal\LogistiTawseel\Facades\Logisti::orders();

        $orders->create()
            ->orderNumber('ORD-1')->authorityId('authority')->deliveryTime('2026-07-25T15:00:00.000Z')
            ->regionId('region')->cityId('city')->coordinates('24.1, 46.2')->storeName('Store')
            ->storeLocation('24.3, 46.4')->categoryId('category')->orderDate('2026-07-25T14:00:00.000Z')
            ->recipientMobileNumber('966555555555')->send();
        $orders->editDeliveryAddress()->referenceCode('R')->regionId('region')->cityId('city')
            ->coordinates('24.1, 46.2')->storeLocation('24.3, 46.4')->send();
        $orders->execute()->referenceCode('R')->executionTime('2026-07-25T15:49:19.459Z')
            ->paymentMethodId('payment')->amounts(150, 30.5, 20)->send();
        $orders->accept()->referenceCode('R')->acceptanceDateTime('2026-07-25T15:43:29.228Z')->send();
        $orders->reject()->referenceCode('R')->send();
        $orders->assignDriver()->referenceCode('R')->idNumber('1016990911')->send();
        $orders->cancel()->referenceCode('R')->cancellationReasonId('reason')->send();

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/order/create')
            && array_keys($request->data()) === ['order']
            && $request['order']['storetName'] === 'Store');
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/edit-order-delivery-address')
            && array_keys($request->data()) === ['deliveryInfo']);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/order/execute')
            && array_keys($request->data()) === ['orderExecutionData']
            && is_float($request['orderExecutionData']['price']));
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/order/accept')
            && $request->data() === ['referenceCode' => 'R', 'acceptanceDateTime' => '2026-07-25T15:43:29.228Z']);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/order/reject')
            && $request->data() === ['referenceCode' => 'R']);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/assign-driver-to-order')
            && $request->data() === ['referenceCode' => 'R', 'idNumber' => '1016990911']);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/order/cancel')
            && $request->data() === ['referenceCode' => 'R', 'cancelationReasonId' => 'reason']);
    }

    public function test_edit_driver_and_raw_driver_payloads_use_v127_shape(): void
    {
        config()->set('logisti.urls.staging', 'https://staging.example.test');
        Http::fake(['*' => Http::response(['status' => true], 200)]);
        $drivers = \YasserElgammal\LogistiTawseel\Facades\Logisti::drivers();

        $drivers->edit()->refrenceCode('D-1')->identityTypeId('identity')->idNumber('1016990911')
            ->dateOfBirth(19900419)->registrationDate('2026-07-25T10:00:00.000Z')
            ->mobile('0555555555')->regionId('region')->cityId('city')
            ->vehicleSequenceNumber('123456789')->send();
        $drivers->createRaw([
            'idNumber' => '1016990911',
            'carTypeId' => 'removed',
            'carNumber' => 'removed',
        ]);

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/driver/edit')
            && array_keys($request->data()) === ['driver']
            && $request['driver']['refrenceCode'] === 'D-1');
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/driver/create')
            && $request->data() === ['driver' => ['idNumber' => '1016990911']]);
    }

    public function test_success_code_zero_and_v127_error_code_mapping(): void
    {
        $response = new LogistiResponse(['status' => true, 'errorCodes' => [0]], 200);

        $this->assertTrue($response->successful());
        $this->assertFalse($response->failed());
        $this->assertSame('VehicleMVPIIsExpired', ErrorCodeMapper::message(136));
        $this->assertNotNull(ErrorCodeMapper::message(84));
    }

    public function test_gateways_send_expected_endpoints(): void
    {
        config()->set('logisti.urls.staging', 'https://staging.example.test');
        Http::fake(['*' => Http::response(['status' => true], 200)]);

        $logisti = \YasserElgammal\LogistiTawseel\Facades\Logisti::getFacadeRoot();
        $logisti->drivers()->get('1016990911');
        $logisti->drivers()->deactivate('1016990911');
        $logisti->orders()->get('REF');
        $logisti->orders()->createRaw(['x' => 1]);
        $logisti->orders()->acceptRaw(['x' => 1]);
        $logisti->orders()->rejectRaw(['x' => 1]);
        $logisti->orders()->assignDriverRaw(['x' => 1]);
        $logisti->orders()->editDeliveryAddressRaw(['x' => 1]);
        $logisti->orders()->executeRaw(['x' => 1]);
        $logisti->orders()->cancelRaw(['x' => 1]);

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/external/api/driver/1016990911'));
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/external/api/driver/deactivate/1016990911'));
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/external/api/order/REF'));
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/external/api/order/cancel'));
    }

    public function test_lookup_endpoints_are_correct(): void
    {
        config()->set('logisti.urls.staging', 'https://staging.example.test');
        Http::fake(['*' => Http::response(['status' => true, 'data' => []], 200)]);

        $lookups = \YasserElgammal\LogistiTawseel\Facades\Logisti::lookups();
        $lookups->authorities(); $lookups->cancellationReasons(); $lookups->regions(); $lookups->categories();
        $lookups->identityTypes(); $lookups->paymentMethods(); $lookups->carTypes(); $lookups->countries(); $lookups->cities('R1');

        foreach ([
            '/external/api/lookup/authorities-list', '/external/api/lookup/cancellation-reasons-list', '/external/api/lookup/regions-list',
            '/external/api/lookup/categories-list', '/external/api/lookup/identity-types-list', '/external/api/lookup/payment-methods-list',
            '/external/api/lookup/car-types-list', '/external/api/lookup/countries-list', '/external/api/lookup/R1/cities-list',
        ] as $endpoint) {
            Http::assertSent(fn ($request) => str_ends_with($request->url(), $endpoint));
        }
    }
}
