<?php

namespace YasserElgammal\LogistiTawseel\Builders\Orders;

use Carbon\CarbonInterface;
use YasserElgammal\LogistiTawseel\Builders\AbstractLogistiBuilder;
use YasserElgammal\LogistiTawseel\Support\Coordinates;

class CreateOrderBuilder extends AbstractLogistiBuilder
{
    protected function endpoint(): string { return '/external/api/order/create'; }
    protected function method(): string { return 'POST'; }
    protected function payloadRoot(): ?string { return 'order'; }
    protected function rules(): array
    {
        return [
            'orderNumber' => ['required', 'string'],
            'authorityId' => ['required', 'string'],
            'deliveryTime' => ['required'],
            'regionId' => ['required', 'string'],
            'cityId' => ['required', 'string'],
            'coordinates' => ['required', 'string'],
            'storetName' => ['required', 'string', 'max:180'],
            'storeLocation' => ['required', 'string'],
            'categoryId' => ['required', 'string'],
            'orderDate' => ['required'],
            'recipientMobileNumber' => ['required', 'regex:/^9665[0-9]{8}$/'],
        ];
    }
    public function orderNumber(string $value): static { return $this->set('orderNumber', $value); }
    public function authorityId(string $value): static { return $this->set('authorityId', $value); }
    public function deliveryTime(CarbonInterface|string $value): static { return $this->set('deliveryTime', $value); }
    public function regionId(string $value): static { return $this->set('regionId', $value); }
    public function cityId(string $value): static { return $this->set('cityId', $value); }
    public function deliveryCoordinates(string|float $lat, string|float $lng): static { return $this->coordinates(Coordinates::format($lat, $lng)); }
    public function coordinates(string $value): static { return $this->set('coordinates', $value); }
    public function storeName(string $value): static { return $this->storetName($value); }
    public function storetName(string $value): static { return $this->set('storetName', $value); }
    public function storeCoordinates(string|float $lat, string|float $lng): static { return $this->storeLocation(Coordinates::format($lat, $lng)); }
    public function storeLocation(string $value): static { return $this->set('storeLocation', $value); }
    public function categoryId(string $value): static { return $this->set('categoryId', $value); }
    public function orderDate(CarbonInterface|string $value): static { return $this->set('orderDate', $value); }
    public function recipientMobileNumber(string $value): static { return $this->set('recipientMobileNumber', $value); }
}
