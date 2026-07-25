<?php

namespace YasserElgammal\LogistiTawseel\Exceptions;

use Illuminate\Support\MessageBag;

class LogistiValidationException extends LogistiException
{
    public function __construct(public MessageBag $errors)
    {
        parent::__construct('Logisti payload validation failed.');
    }
}
