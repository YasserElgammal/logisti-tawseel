<?php

namespace YasserElgammal\LogistiTawseel\Gateways;

use YasserElgammal\LogistiTawseel\Builders\Orders\AcceptOrderBuilder;
use YasserElgammal\LogistiTawseel\Builders\Orders\AssignDriverBuilder;
use YasserElgammal\LogistiTawseel\Builders\Orders\CancelOrderBuilder;
use YasserElgammal\LogistiTawseel\Builders\Orders\CreateOrderBuilder;
use YasserElgammal\LogistiTawseel\Builders\Orders\EditDeliveryAddressBuilder;
use YasserElgammal\LogistiTawseel\Builders\Orders\ExecuteOrderBuilder;
use YasserElgammal\LogistiTawseel\Builders\Orders\RejectOrderBuilder;
use YasserElgammal\LogistiTawseel\Http\LogistiClient;
use YasserElgammal\LogistiTawseel\Http\LogistiResponse;

class OrderGateway
{
    public function __construct(private LogistiClient $client) {}
    public function create(): CreateOrderBuilder { return new CreateOrderBuilder($this->client); }
    public function accept(): AcceptOrderBuilder { return new AcceptOrderBuilder($this->client); }
    public function reject(): RejectOrderBuilder { return new RejectOrderBuilder($this->client); }
    public function assignDriver(): AssignDriverBuilder { return new AssignDriverBuilder($this->client); }
    public function editDeliveryAddress(): EditDeliveryAddressBuilder { return new EditDeliveryAddressBuilder($this->client); }
    public function execute(): ExecuteOrderBuilder { return new ExecuteOrderBuilder($this->client); }
    public function cancel(): CancelOrderBuilder { return new CancelOrderBuilder($this->client); }
    public function get(string $referenceCode): LogistiResponse { return $this->client->get('/external/api/order/' . $referenceCode); }
    public function createRaw(array $payload): LogistiResponse { return $this->client->post('/external/api/order/create', $this->wrap($payload, 'order')); }
    public function acceptRaw(array $payload): LogistiResponse { return $this->client->post('/external/api/order/accept', $payload); }
    public function rejectRaw(array $payload): LogistiResponse { return $this->client->post('/external/api/order/reject', $payload); }
    public function assignDriverRaw(array $payload): LogistiResponse { return $this->client->post('/external/api/order/assign-driver-to-order', $payload); }
    public function editDeliveryAddressRaw(array $payload): LogistiResponse { return $this->client->post('/external/api/order/edit-order-delivery-address', $this->wrap($payload, 'deliveryInfo')); }
    public function executeRaw(array $payload): LogistiResponse { return $this->client->post('/external/api/order/execute', $this->wrap($payload, 'orderExecutionData')); }
    public function cancelRaw(array $payload): LogistiResponse { return $this->client->post('/external/api/order/cancel', $payload); }

    private function wrap(array $payload, string $root): array
    {
        return isset($payload[$root]) && is_array($payload[$root])
            ? $payload
            : [$root => $payload];
    }
}
