<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SteadFast Fraud Score
    |--------------------------------------------------------------------------
    |
    | Uses the existing courier API keys (courierhub.couriers.steadfast.*)
    | for GET /fraud_check/score/{phone}. Enabled by default; the courier
    | settings migration overrides these defaults at runtime.
    |
    */

    'steadfast' => [
        'enabled' => env('FRAUD_STEADFAST_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | BD Courier Fraud Check
    |--------------------------------------------------------------------------
    |
    | POST {base_url}/courier-check with a Bearer API key. Keep the key on
    | the server only — never expose it to frontend JavaScript.
    |
    */

    'bdcourier' => [
        'enabled' => env('FRAUD_BDCOURIER_ENABLED', false),
        'base_url' => env('BDCOURIER_API_URL', 'https://api.bdcourier.com'),
        'api_key' => env('BDCOURIER_API_KEY'),
    ],

];
