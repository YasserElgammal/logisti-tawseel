<?php

namespace YasserElgammal\LogistiTawseel\Http;

use Illuminate\Support\Arr;

class LogistiResponse
{
    public function __construct(
        private array $body,
        private int $statusCode,
        private ?string $raw = null
    ) {
    }

    public function successful(): bool
    {
        $httpOk = $this->statusCode >= 200 && $this->statusCode < 300;

        if (! array_key_exists('status', $this->body)) {
            return $httpOk;
        }

        return $httpOk && (bool) $this->body['status'] === true;
    }

    public function failed(): bool
    {
        return ! $this->successful();
    }

    public function data(?string $key = null): mixed
    {
        $data = $this->body['data'] ?? $this->body;

        return $key === null ? $data : Arr::get($data, $key);
    }

    public function errorCodes(): array
    {
        $codes = $this->body['errorCodes']
            ?? $this->body['error_codes']
            ?? $this->body['errorCode']
            ?? $this->body['error_code']
            ?? $this->body['errors']
            ?? $this->body['data']['errorCodes']
            ?? [];

        return is_array($codes) ? array_values($codes) : [$codes];
    }

    public function body(): array
    {
        return $this->body;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function raw(): ?string
    {
        return $this->raw;
    }
}
