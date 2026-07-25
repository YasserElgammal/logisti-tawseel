<?php

namespace YasserElgammal\LogistiTawseel\Builders\Orders;

use Carbon\CarbonInterface;
use YasserElgammal\LogistiTawseel\Builders\AbstractLogistiBuilder;

class AcceptOrderBuilder extends AbstractLogistiBuilder
{
    protected function endpoint(): string { return '/external/api/order/accept'; }
    protected function method(): string { return 'POST'; }
    protected function rules(): array { return ['referenceCode' => ['required', 'string'], 'acceptanceDateTime' => ['required']]; }
    public function referenceCode(string $value): static { return $this->set('referenceCode', $value); }
    public function acceptanceDateTime(CarbonInterface|string $value): static { return $this->set('acceptanceDateTime', $value); }
}