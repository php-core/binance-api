<?php

// Helper function for Laravel's env() if not available
if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false) {
            return $default;
        }
        return $value;
    }
}

return [
    /*
    |--------------------------------------------------------------------------
    | Binance API Key
    |--------------------------------------------------------------------------
    |
    | Your Binance API key. You can generate this from your Binance account
    | settings under API Management.
    |
    */
    'api_key' => env('BINANCE_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Binance API Secret
    |--------------------------------------------------------------------------
    |
    | Your Binance API secret. Keep this secure and never commit it to
    | version control. Always use environment variables.
    |
    */
    'api_secret' => env('BINANCE_API_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Testnet Mode
    |--------------------------------------------------------------------------
    |
    | Enable this to use Binance Testnet instead of the production API.
    | Useful for development and testing without risking real funds.
    |
    */
    'testnet' => env('BINANCE_TESTNET', false),

    /*
    |--------------------------------------------------------------------------
    | Proxy Configuration
    |--------------------------------------------------------------------------
    |
    | If you're using a proxy to connect to Binance API, configure it here.
    | These settings are only used when not in testnet mode.
    |
    */
    'proxy' => [
        'host' => env('BINANCE_PROXY_HOST', null),
        'port' => env('BINANCE_PROXY_PORT', null),
        'protocol' => env('BINANCE_PROXY_PROTOCOL', 'https'),
    ],
];
