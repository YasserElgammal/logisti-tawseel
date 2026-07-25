<?php

namespace YasserElgammal\LogistiTawseel\Builders\Orders;

use YasserElgammal\LogistiTawseel\Builders\AbstractLogistiBuilder;

class AssignDriverBuilder extends AbstractLogistiBuilder
{
    protected function endpoint(): string { return '/external/api/order/assign-driver-to-order'; }
    protected function method(): string { return 'POST'; }
    protected function rules(): array { return ['referenceCode' => ['required', 'string'], 'idNumber' => ['required', 'digits:10', 'regex:/^[12]/']]; }
    public function referenceCode(string $value): static { return $this->set('referenceCode', $value); }
    public function idNumber(string $value): static { return $this->set('idNumber', $value); }
}