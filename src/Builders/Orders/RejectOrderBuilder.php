<?php

namespace YasserElgammal\LogistiTawseel\Builders\Orders;

use YasserElgammal\LogistiTawseel\Builders\AbstractLogistiBuilder;

class RejectOrderBuilder extends AbstractLogistiBuilder
{
    protected function endpoint(): string { return '/external/api/order/reject'; }
    protected function method(): string { return 'POST'; }
    protected function rules(): array
    {
        // The integration guide has a copy-paste mistake for reject order; request schema contains referenceCode only.
        return ['referenceCode' => ['required', 'string']];
    }
    public function referenceCode(string $value): static { return $this->set('referenceCode', $value); }
}