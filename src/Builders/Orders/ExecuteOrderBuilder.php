<?php

namespace YasserElgammal\LogistiTawseel\Builders\Orders;

use Carbon\CarbonInterface;
use YasserElgammal\LogistiTawseel\Builders\AbstractLogistiBuilder;
use YasserElgammal\LogistiTawseel\Exceptions\LogistiValidationException;

class ExecuteOrderBuilder extends AbstractLogistiBuilder
{
    protected function endpoint(): string { return '/external/api/order/execute'; }
    protected function method(): string { return 'POST'; }
    protected function payloadRoot(): ?string { return 'orderExecutionData'; }
    protected function rules(): array
    {
        return [
            'referenceCode' => ['required', 'string'],
            'executionTime' => ['required'],
            'paymentMethodId' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'priceWithoutDelivery' => ['required', 'numeric', 'min:0'],
            'deliveryPrice' => ['required', 'numeric', 'min:0'],
            'driverIncome' => ['required', 'numeric', 'min:0'],
        ];
    }
    public function referenceCode(string $value): static { return $this->set('referenceCode', $value); }
    public function executionTime(CarbonInterface|string $value): static { return $this->set('executionTime', $value); }
    public function paymentMethodId(string $value): static { return $this->set('paymentMethodId', $value); }
    public function price(float|int $value): static { return $this->set('price', $value); }
    public function priceWithoutDelivery(float|int $value): static { return $this->set('priceWithoutDelivery', $value); }
    public function deliveryPrice(float|int $value): static { return $this->set('deliveryPrice', $value); }
    public function driverIncome(float|int $value): static { return $this->set('driverIncome', $value); }
    public function amounts(float $priceWithoutDelivery, float $deliveryPrice, float $driverIncome): static
    {
        return $this->priceWithoutDelivery($priceWithoutDelivery)
            ->deliveryPrice($deliveryPrice)
            ->driverIncome($driverIncome)
            ->price($priceWithoutDelivery + $deliveryPrice);
    }
    protected function afterValidation(): void
    {
        $expected = (float) $this->payload['priceWithoutDelivery'] + (float) $this->payload['deliveryPrice'];
        if (abs((float) $this->payload['price'] - $expected) > 0.00001) {
            throw new LogistiValidationException(new \Illuminate\Support\MessageBag([
                'price' => ['The price must equal priceWithoutDelivery plus deliveryPrice.'],
            ]));
        }
    }
}
