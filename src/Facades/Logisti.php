<?php

namespace YasserElgammal\LogistiTawseel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \YasserElgammal\LogistiTawseel\Gateways\DriverGateway drivers()
 * @method static \YasserElgammal\LogistiTawseel\Gateways\OrderGateway orders()
 * @method static \YasserElgammal\LogistiTawseel\Gateways\LookupGateway lookups()
 * @method static \YasserElgammal\LogistiTawseel\Gateways\ContactInfoGateway contactInfo()
 */
class Logisti extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'logisti';
    }
}
