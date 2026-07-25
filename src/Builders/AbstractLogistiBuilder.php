<?php

namespace YasserElgammal\LogistiTawseel\Builders;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Validator;
use YasserElgammal\LogistiTawseel\Exceptions\LogistiValidationException;
use YasserElgammal\LogistiTawseel\Http\LogistiClient;
use YasserElgammal\LogistiTawseel\Http\LogistiResponse;

abstract class AbstractLogistiBuilder
{
    protected array $payload = [];

    public function __construct(protected LogistiClient $client)
    {
    }

    abstract protected function endpoint(): string;

    abstract protected function method(): string;

    abstract protected function rules(): array;

    protected function payloadRoot(): ?string
    {
        return null;
    }

    protected function preparePayload(array $payload): array
    {
        return $payload;
    }

    protected function set(string $key, mixed $value): static
    {
        $this->payload[$key] = $this->normalizeValue($value);

        return $this;
    }

    public function toArray(): array
    {
        return $this->payload;
    }

    public function requestPayload(): array
    {
        $payload = $this->preparePayload($this->payload);
        $root = $this->payloadRoot();

        return $root === null ? $payload : [$root => $payload];
    }

    public function validate(): static
    {
        $validator = Validator::make($this->payload, $this->rules());

        if ($validator->fails()) {
            throw new LogistiValidationException($validator->errors());
        }

        $this->afterValidation();

        return $this;
    }

    public function send(): LogistiResponse
    {
        $this->validate();

        return $this->method() === 'GET'
            ? $this->client->get($this->endpoint(), $this->requestPayload())
            : $this->client->post($this->endpoint(), $this->requestPayload());
    }

    protected function afterValidation(): void
    {
    }

    protected function normalizeValue(mixed $value): mixed
    {
        if ($value instanceof CarbonInterface) {
            return $value->toIso8601String();
        }

        return $value;
    }
}
