<?php

namespace YasserElgammal\LogistiTawseel\Http;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;
use YasserElgammal\LogistiTawseel\Exceptions\LogistiApiException;
use YasserElgammal\LogistiTawseel\Exceptions\LogistiConnectionException;
use YasserElgammal\LogistiTawseel\Models\LogistiRequest;

class LogistiClient
{
    public function get(string $endpoint, array $query = []): LogistiResponse
    {
        return $this->send('GET', $endpoint, $query);
    }

    public function post(string $endpoint, array $payload = []): LogistiResponse
    {
        return $this->send('POST', $endpoint, $payload);
    }

    public function baseUrl(): string
    {
        $environment = (string) config('logisti.environment', 'staging');

        return rtrim((string) config("logisti.urls.$environment", config('logisti.urls.staging')), '/');
    }

    protected function send(string $method, string $endpoint, array $payload = []): LogistiResponse
    {
        $startedAt = microtime(true);
        $response = null;
        $logistiResponse = null;

        try {
            $request = Http::withHeaders([
                'Content-Type' => 'application/json',
                'app-id' => (string) config('logisti.app_id'),
                'app-key' => (string) config('logisti.app_key'),
            ])->timeout((int) config('logisti.timeout', 30));

            $request = $this->withRetry($request);

            $url = $this->baseUrl() . '/' . ltrim($endpoint, '/');
            $response = $method === 'GET' ? $request->get($url, $payload) : $request->post($url, $payload);
            $logistiResponse = $this->toLogistiResponse($response);

            $this->log($endpoint, $method, $payload, $logistiResponse, $startedAt);

            if ($logistiResponse->failed() && (bool) config('logisti.throw_exceptions', true)) {
                throw new LogistiApiException($logistiResponse);
            }

            return $logistiResponse;
        } catch (ConnectionException $exception) {
            $logistiResponse = new LogistiResponse([], 0, null);
            $this->log($endpoint, $method, $payload, $logistiResponse, $startedAt, $exception->getMessage());

            if ((bool) config('logisti.throw_exceptions', true)) {
                throw new LogistiConnectionException($exception->getMessage(), 0, $exception);
            }

            return $logistiResponse;
        } catch (Throwable $exception) {
            if ($exception instanceof LogistiApiException) {
                throw $exception;
            }

            $status = $response instanceof Response ? $response->status() : 0;
            $logistiResponse = $logistiResponse ?: new LogistiResponse([], $status, null);
            $this->log($endpoint, $method, $payload, $logistiResponse, $startedAt, $exception->getMessage());

            if ((bool) config('logisti.throw_exceptions', true)) {
                throw $exception;
            }

            return $logistiResponse;
        }
    }

    protected function withRetry($request)
    {
        $times = (int) config('logisti.retry_times', 2);
        $sleep = (int) config('logisti.retry_sleep', 500);
        $method = new \ReflectionMethod($request, 'retry');

        if ($method->getNumberOfParameters() >= 4) {
            return $request->retry($times, $sleep, null, false);
        }

        return $request->retry($times, $sleep);
    }
    protected function toLogistiResponse(Response $response): LogistiResponse
    {
        $body = $response->json();

        return new LogistiResponse(is_array($body) ? $body : [], $response->status(), $response->body());
    }

    protected function log(string $endpoint, string $method, array $payload, LogistiResponse $response, float $startedAt, ?string $exceptionMessage = null): void
    {
        if (! (bool) config('logisti.log_requests', true)) {
            return;
        }

        try {
            LogistiRequest::query()->create([
                'endpoint' => $endpoint,
                'method' => $method,
                'request_payload' => $payload,
                'response_payload' => $response->body(),
                'status_code' => $response->statusCode() ?: null,
                'error_codes' => $response->errorCodes(),
                'successful' => $response->successful(),
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'exception_message' => $exceptionMessage,
            ]);
        } catch (Throwable) {
            // Logging must never break API calls.
        }
    }
}