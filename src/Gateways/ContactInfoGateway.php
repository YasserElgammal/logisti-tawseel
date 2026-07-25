<?php

namespace YasserElgammal\LogistiTawseel\Gateways;

use YasserElgammal\LogistiTawseel\Builders\ContactInfo\CreateOrUpdateContactInfoBuilder;
use YasserElgammal\LogistiTawseel\Http\LogistiClient;
use YasserElgammal\LogistiTawseel\Http\LogistiResponse;

class ContactInfoGateway
{
    public function __construct(private LogistiClient $client) {}
    public function createOrUpdate(): CreateOrUpdateContactInfoBuilder { return new CreateOrUpdateContactInfoBuilder($this->client); }
    public function get(): LogistiResponse { return $this->client->get('/external/api/app/contact-info'); }
    public function createOrUpdateRaw(array $payload): LogistiResponse { return $this->client->post('/external/api/app/contact-info', $payload); }
}