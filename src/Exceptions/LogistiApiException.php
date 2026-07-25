<?php

namespace YasserElgammal\LogistiTawseel\Exceptions;

use YasserElgammal\LogistiTawseel\Http\LogistiResponse;

class LogistiApiException extends LogistiException
{
    public function __construct(public LogistiResponse $response)
    {
        parent::__construct('Logisti API request failed.', $response->statusCode());
    }
}
