<?php

namespace YasserElgammal\LogistiTawseel\Support;

class Coordinates
{
    public static function format(string|float $lat, string|float $lng): string
    {
        return trim((string) $lat) . ', ' . trim((string) $lng);
    }
}