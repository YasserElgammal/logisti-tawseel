<?php

namespace YasserElgammal\LogistiTawseel\Builders\Drivers;

use Carbon\CarbonInterface;
use YasserElgammal\LogistiTawseel\Builders\AbstractLogistiBuilder;

class CreateDriverBuilder extends AbstractLogistiBuilder
{
    protected function endpoint(): string { return '/external/api/driver/create'; }
    protected function method(): string { return 'POST'; }
    protected function rules(): array
    {
        return [
            'identityTypeId' => ['required', 'string'],
            'idNumber' => ['required', 'string', 'regex:/^[12]\d{9}$/'],
            'dateOfBirth' => ['required', 'integer', 'digits:8'],
            'registrationDate' => ['required', 'date'],
            'mobile' => ['required', 'string', 'regex:/^05\d{8}$/'],
            'regionId' => ['required', 'string'],
            'cityId' => ['required', 'string'],
            'vehicleSequenceNumber' => ['required'],
        ];
    }

    protected function payloadRoot(): ?string
    {
        return 'driver';
    }

    public function identityTypeId(string $value): static { return $this->set('identityTypeId', $value); }
    public function idNumber(string $value): static { return $this->set('idNumber', $value); }
    public function dateOfBirth(int|string $value): static { return $this->set('dateOfBirth', $value); }
    public function registrationDate(CarbonInterface|string $value): static { return $this->set('registrationDate', $value); }
    public function mobile(string $value): static { return $this->set('mobile', $value); }
    public function regionId(string $value): static { return $this->set('regionId', $value); }
    public function cityId(string $value): static { return $this->set('cityId', $value); }
    public function vehicleSequenceNumber(string|int $value): static { return $this->set('vehicleSequenceNumber', $value); }
}
