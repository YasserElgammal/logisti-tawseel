<?php

namespace YasserElgammal\LogistiTawseel\Gateways;

use YasserElgammal\LogistiTawseel\Http\LogistiClient;
use YasserElgammal\LogistiTawseel\Http\LogistiResponse;

class LookupGateway
{
    public function __construct(private LogistiClient $client) {}
    public function authorities(): LogistiResponse { return $this->client->get('/external/api/lookup/authorities-list'); }
    public function cancellationReasons(): LogistiResponse { return $this->client->get('/external/api/lookup/cancellation-reasons-list'); }
    public function regions(): LogistiResponse { return $this->client->get('/external/api/lookup/regions-list'); }
    public function categories(): LogistiResponse { return $this->client->get('/external/api/lookup/categories-list'); }
    public function identityTypes(): LogistiResponse { return $this->client->get('/external/api/lookup/identity-types-list'); }
    public function paymentMethods(): LogistiResponse { return $this->client->get('/external/api/lookup/payment-methods-list'); }
    public function carTypes(): LogistiResponse { return $this->client->get('/external/api/lookup/car-types-list'); }
    public function countries(): LogistiResponse { return $this->client->get('/external/api/lookup/countries-list'); }
    public function cities(string $regionId): LogistiResponse { return $this->client->get('/external/api/lookup/' . $regionId . '/cities-list'); }
}