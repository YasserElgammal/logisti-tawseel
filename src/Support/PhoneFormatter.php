<?php

namespace YasserElgammal\LogistiTawseel\Support;

class PhoneFormatter
{
    public static function digits(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?: $phone;
    }
}