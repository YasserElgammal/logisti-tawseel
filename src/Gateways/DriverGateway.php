<?php

namespace YasserElgammal\LogistiTawseel\Gateways;

use YasserElgammal\LogistiTawseel\Builders\Drivers\CreateDriverBuilder;
use YasserElgammal\LogistiTawseel\Builders\Drivers\EditDriverBuilder;
use YasserElgammal\LogistiTawseel\Http\LogistiClient;
use YasserElgammal\LogistiTawseel\Http\LogistiResponse;

class DriverGateway
{
    public function __construct(private LogistiClient $client) {}
    public function create(): CreateDriverBuilder { return new CreateDriverBuilder($this->client); }
    public function edit(): EditDriverBuilder { return new EditDriverBuilder($this->client); }
    public function deactivate(string $idNumber): LogistiResponse { return $this->client->post('/external/api/driver/deactivate/' . $idNumber); }
    public function get(string $idNumber): LogistiResponse { return $this->client->get('/external/api/driver/' . $idNumber); }
    public function createRaw(array $payload): LogistiResponse
    {
        return $this->client->post('/external/api/driver/create', $this->driverPayload($payload));
    }

    public function editRaw(array $payload): LogistiResponse
    {
        return $this->client->post('/external/api/driver/edit', $this->driverPayload($payload));
    }

    private function driverPayload(array $payload): array
    {
        $driver = isset($payload['driver']) && is_array($payload['driver'])
            ? $payload['driver']
            : $payload;

        unset($driver['carTypeId'], $driver['carNumber']);

        return ['driver' => $driver];
    }
}
