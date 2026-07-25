<?php

namespace YasserElgammal\LogistiTawseel\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use YasserElgammal\LogistiTawseel\LogistiServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [LogistiServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('logisti.app_id', 'app-id');
        $app['config']->set('logisti.app_key', 'app-key');
        $app['config']->set('logisti.log_requests', false);
        $app['config']->set('logisti.throw_exceptions', false);
    }

    protected function migrateLogistiTables(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->artisan('migrate')->run();
    }
}