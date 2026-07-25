<?php

namespace YasserElgammal\LogistiTawseel\Builders\ContactInfo;

use YasserElgammal\LogistiTawseel\Builders\AbstractLogistiBuilder;

class CreateOrUpdateContactInfoBuilder extends AbstractLogistiBuilder
{
    protected array $contactInfo = [];

    protected function endpoint(): string { return '/external/api/app/contact-info'; }
    protected function method(): string { return 'POST'; }
    protected function rules(): array
    {
        return [
            'appContactInfo.responsibleName' => ['required', 'string'],
            'appContactInfo.responsibleEmail' => ['required', 'email'],
            'appContactInfo.responsibleMobileNumber' => ['required', 'regex:/^9665[0-9]{8}$/'],
            'appContactInfo.technicalName' => ['required', 'string'],
            'appContactInfo.technicalEmail' => ['required', 'email'],
            'appContactInfo.technicalMobileNumber' => ['required', 'regex:/^9665[0-9]{8}$/'],
        ];
    }
    protected function setContact(string $key, string $value): static
    {
        $this->payload['appContactInfo'][$key] = $value;
        return $this;
    }
    public function responsibleName(string $value): static { return $this->setContact('responsibleName', $value); }
    public function responsibleEmail(string $value): static { return $this->setContact('responsibleEmail', $value); }
    public function responsibleMobileNumber(string $value): static { return $this->setContact('responsibleMobileNumber', $value); }
    public function technicalName(string $value): static { return $this->setContact('technicalName', $value); }
    public function technicalEmail(string $value): static { return $this->setContact('technicalEmail', $value); }
    public function technicalMobileNumber(string $value): static { return $this->setContact('technicalMobileNumber', $value); }
}