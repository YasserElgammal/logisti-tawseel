<?php

namespace YasserElgammal\LogistiTawseel\Tests\Unit;

use Carbon\CarbonImmutable;
use YasserElgammal\LogistiTawseel\Exceptions\LogistiValidationException;
use YasserElgammal\LogistiTawseel\Http\LogistiClient;
use YasserElgammal\LogistiTawseel\Tests\TestCase;

class BuilderTest extends TestCase
{
    private function client(): LogistiClient
    {
        return app(LogistiClient::class);
    }

    public function test_create_driver_builder_to_array(): void
    {
        $payload = \YasserElgammal\LogistiTawseel\Facades\Logisti::drivers()->create()
            ->identityTypeId('1')->idNumber('1016990911')->dateOfBirth(14190408)
            ->registrationDate(CarbonImmutable::parse('2026-01-01'))->mobile('0555555555')
            ->regionId('r')->cityId('c')
            ->vehicleSequenceNumber('277525810')->toArray();

        $this->assertSame('1016990911', $payload['idNumber']);
        $this->assertSame('277525810', $payload['vehicleSequenceNumber']);
        $this->assertArrayNotHasKey('carNumber', $payload);
    }

    public function test_edit_driver_builder_to_array(): void
    {
        $payload = \YasserElgammal\LogistiTawseel\Facades\Logisti::drivers()->edit()
            ->refrenceCode('D-1')->identityTypeId('1')->idNumber('1016990911')->dateOfBirth('14190408')
            ->registrationDate('2026-01-01')->mobile('0555555555')->regionId('r')->cityId('c')
            ->vehicleSequenceNumber(123)->toArray();

        $this->assertSame('D-1', $payload['refrenceCode']);
        $this->assertArrayNotHasKey('carNumber', $payload);
    }

    public function test_create_order_builder_to_array(): void
    {
        $payload = \YasserElgammal\LogistiTawseel\Facades\Logisti::orders()->create()
            ->orderNumber('ORD-1')->authorityId('a')->deliveryTime('2026-01-01T10:00:00+03:00')
            ->regionId('r')->cityId('c')->deliveryCoordinates('24.1', '46.2')->storeName('Store')
            ->storeCoordinates('24.3', '46.4')->categoryId('cat')->orderDate('2026-01-01')
            ->recipientMobileNumber('966555555555')->toArray();

        $this->assertSame('24.1, 46.2', $payload['coordinates']);
        $this->assertSame('Store', $payload['storetName']);
        $this->assertSame('24.3, 46.4', $payload['storeLocation']);
    }

    public function test_order_action_builders_to_array(): void
    {
        $orders = \YasserElgammal\LogistiTawseel\Facades\Logisti::orders();
        $this->assertSame(['referenceCode' => 'R', 'acceptanceDateTime' => 'now'], $orders->accept()->referenceCode('R')->acceptanceDateTime('now')->toArray());
        $this->assertSame(['referenceCode' => 'R'], $orders->reject()->referenceCode('R')->toArray());
        $this->assertSame(['referenceCode' => 'R', 'idNumber' => '1016990911'], $orders->assignDriver()->referenceCode('R')->idNumber('1016990911')->toArray());
        $this->assertSame('24, 46', $orders->editDeliveryAddress()->referenceCode('R')->regionId('r')->cityId('c')->deliveryCoordinates('24', '46')->storeCoordinates('25', '47')->toArray()['coordinates']);
        $this->assertSame(180.5, $orders->execute()->referenceCode('R')->executionTime('now')->paymentMethodId('p')->amounts(150.0, 30.5, 20.0)->toArray()['price']);
        $this->assertSame(['referenceCode' => 'R', 'cancelationReasonId' => 'C'], $orders->cancel()->referenceCode('R')->cancellationReasonId('C')->toArray());
    }

    public function test_contact_info_builder_wraps_payload(): void
    {
        $payload = \YasserElgammal\LogistiTawseel\Facades\Logisti::contactInfo()->createOrUpdate()
            ->responsibleName('A')->responsibleEmail('a@example.com')->responsibleMobileNumber('966555555555')
            ->technicalName('B')->technicalEmail('b@example.com')->technicalMobileNumber('966566666666')->toArray();

        $this->assertArrayHasKey('appContactInfo', $payload);
        $this->assertSame('A', $payload['appContactInfo']['responsibleName']);
    }

    public function test_validation_failures_happen_before_request(): void
    {
        $this->expectException(LogistiValidationException::class);
        \YasserElgammal\LogistiTawseel\Facades\Logisti::drivers()->create()
            ->identityTypeId('1')->idNumber('999')->dateOfBirth('14190408')->registrationDate('now')
            ->mobile('0555555555')->regionId('r')->cityId('c')->vehicleSequenceNumber('1')
            ->validate();
    }

    public function test_cancel_without_identifier_fails(): void
    {
        $this->expectException(LogistiValidationException::class);
        \YasserElgammal\LogistiTawseel\Facades\Logisti::orders()->cancel()->cancelationReasonId('C')->validate();
    }
    public function test_invalid_mobile_fails_before_request(): void
    {
        $this->expectException(LogistiValidationException::class);
        \YasserElgammal\LogistiTawseel\Facades\Logisti::drivers()->create()
            ->identityTypeId('1')->idNumber('1016990911')->dateOfBirth('14190408')->registrationDate('now')
            ->mobile('966555555555')->regionId('r')->cityId('c')->vehicleSequenceNumber('1')
            ->validate();
    }

    public function test_invalid_recipient_mobile_fails_before_request(): void
    {
        $this->expectException(LogistiValidationException::class);
        \YasserElgammal\LogistiTawseel\Facades\Logisti::orders()->create()
            ->orderNumber('ORD')->authorityId('a')->deliveryTime('now')->regionId('r')->cityId('c')
            ->coordinates('24, 46')->storetName('Store')->storeLocation('24, 46')->categoryId('cat')->orderDate('now')
            ->recipientMobileNumber('0555555555')->validate();
    }

    public function test_execute_order_missing_price_fields_fails(): void
    {
        $this->expectException(LogistiValidationException::class);
        \YasserElgammal\LogistiTawseel\Facades\Logisti::orders()->execute()
            ->referenceCode('R')->executionTime('now')->paymentMethodId('p')->validate();
    }
}
