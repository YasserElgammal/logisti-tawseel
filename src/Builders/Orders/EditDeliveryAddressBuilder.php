<?php

namespace YasserElgammal\LogistiTawseel\Builders\Orders;

use YasserElgammal\LogistiTawseel\Builders\AbstractLogistiBuilder;
use YasserElgammal\LogistiTawseel\Support\Coordinates;

class EditDeliveryAddressBuilder extends AbstractLogistiBuilder
{
    protected function endpoint(): string { return '/external/api/order/edit-order-delivery-address'; }
    protected function method(): string { return 'POST'; }
    protected function payloadRoot(): ?string { return 'deliveryInfo'; }
    protected function rules(): array
    {
        return [
            'referenceCode' => ['required', 'string'],
            'regionId' => ['required', 'string'],
            'cityId' => ['required', 'string'],
            'coordinates' => ['required', 'string'],
            'storeLocation' => ['required', 'string'],
        ];
    }
    public function referenceCode(string $value): static { return $this->set('referenceCode', $value); }
    public function regionId(string $value): static { return $this->set('regionId', $value); }
    public function cityId(string $value): static { return $this->set('cityId', $value); }
    public function deliveryCoordinates(string|float $lat, string|float $lng): static { return $this->coordinates(Coordinates::format($lat, $lng)); }
    public function coordinates(string $value): static { return $this->set('coordinates', $value); }
    public function storeCoordinates(string|float $lat, string|float $lng): static { return $this->storeLocation(Coordinates::format($lat, $lng)); }
    public function storeLocation(string $value): static { return $this->set('storeLocation', $value); }
}
