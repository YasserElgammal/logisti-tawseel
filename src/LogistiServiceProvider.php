<?php

namespace YasserElgammal\LogistiTawseel;

use Illuminate\Support\ServiceProvider;
use YasserElgammal\LogistiTawseel\Http\LogistiClient;

class LogistiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/logisti.php', 'logisti');

        $this->app->singleton(LogistiClient::class);
        $this->app->singleton('logisti', fn ($app) => new LogistiManager($app->make(LogistiClient::class)));
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/logisti.php' => config_path('logisti.php'),
        ], 'logisti-config');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}