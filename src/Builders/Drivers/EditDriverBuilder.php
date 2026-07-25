<?php

namespace YasserElgammal\LogistiTawseel\Builders\Drivers;

class EditDriverBuilder extends CreateDriverBuilder
{
    protected function endpoint(): string { return '/external/api/driver/edit'; }

    protected function rules(): array
    {
        return ['refrenceCode' => ['required', 'string']] + parent::rules();
    }

    public function refrenceCode(string $value): static
    {
        return $this->set('refrenceCode', $value);
    }
}