<?php

namespace YasserElgammal\LogistiTawseel\Builders\Orders;

use YasserElgammal\LogistiTawseel\Builders\AbstractLogistiBuilder;

class CancelOrderBuilder extends AbstractLogistiBuilder
{
    protected function endpoint(): string { return '/external/api/order/cancel'; }
    protected function method(): string { return 'POST'; }
    protected function rules(): array
    {
        return [
            'referenceCode' => ['required', 'string'],
            'cancelationReasonId' => ['required', 'string'],
        ];
    }
    public function referenceCode(string $value): static { return $this->set('referenceCode', $value); }
    public function cancelationReasonId(string $value): static { return $this->set('cancelationReasonId', $value); }
    public function cancellationReasonId(string $value): static { return $this->cancelationReasonId($value); }
}
