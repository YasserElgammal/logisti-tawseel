<?php

namespace YasserElgammal\LogistiTawseel;

use YasserElgammal\LogistiTawseel\Gateways\ContactInfoGateway;
use YasserElgammal\LogistiTawseel\Gateways\DriverGateway;
use YasserElgammal\LogistiTawseel\Gateways\LookupGateway;
use YasserElgammal\LogistiTawseel\Gateways\OrderGateway;
use YasserElgammal\LogistiTawseel\Http\LogistiClient;

class LogistiManager
{
    public function __construct(private LogistiClient $client)
    {
    }

    public function drivers(): DriverGateway
    {
        return new DriverGateway($this->client);
    }

    public function orders(): OrderGateway
    {
        return new OrderGateway($this->client);
    }

    public function lookups(): LookupGateway
    {
        return new LookupGateway($this->client);
    }

    public function contactInfo(): ContactInfoGateway
    {
        return new ContactInfoGateway($this->client);
    }
}
