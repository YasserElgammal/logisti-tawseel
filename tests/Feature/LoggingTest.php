<?php

namespace YasserElgammal\LogistiTawseel\Tests\Feature;

use Illuminate\Support\Facades\Http;
use YasserElgammal\LogistiTawseel\Http\LogistiClient;
use YasserElgammal\LogistiTawseel\Models\LogistiRequest;
use YasserElgammal\LogistiTawseel\Tests\TestCase;

class LoggingTest extends TestCase
{
    public function test_successful_and_failed_requests_are_logged(): void
    {
        $this->migrateLogistiTables();
        config()->set('logisti.log_requests', true);
        config()->set('logisti.urls.staging', 'https://staging.example.test');
        Http::fakeSequence()->push(['status' => true], 200)->push(['status' => false, 'errorCodes' => [47]], 422);

        app(LogistiClient::class)->get('/ok');
        app(LogistiClient::class)->get('/failed');

        $this->assertSame(2, LogistiRequest::query()->count());
        $this->assertTrue((bool) LogistiRequest::query()->where('endpoint', '/ok')->first()->successful);
        $this->assertFalse((bool) LogistiRequest::query()->where('endpoint', '/failed')->first()->successful);
    }
}

