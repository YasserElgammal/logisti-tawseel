<?php

return [
    'enabled' => env('LOGISTI_ENABLED', true),
    'environment' => env('LOGISTI_ENVIRONMENT', 'staging'),

    'urls' => [
        'staging' => env('LOGISTI_STAGING_URL', 'https://tawseel-stg.api.elm.sa'),
        'production' => env('LOGISTI_PRODUCTION_URL', 'https://tawseel.api.elm.sa'),
    ],

    'app_id' => env('LOGISTI_APP_ID'),
    'app_key' => env('LOGISTI_APP_KEY'),

    'timeout' => env('LOGISTI_TIMEOUT', 30),
    'retry_times' => env('LOGISTI_RETRY_TIMES', 2),
    'retry_sleep' => env('LOGISTI_RETRY_SLEEP', 500),

    'log_requests' => env('LOGISTI_LOG_REQUESTS', true),
    'throw_exceptions' => env('LOGISTI_THROW_EXCEPTIONS', true),
];
