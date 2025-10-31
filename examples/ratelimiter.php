<?php

require '../binance-api.php';
require '../binance-api-rate-limiter.php';

// use config from ~/.confg/php-core/binance-api.json
$api = new PHPCore\BinanceApi\BinanceApi();
$api = new PHPCore\BinanceApi\RateLimiter($api);

// Get latest price of a symbol
$price = $api->price("BNBBTC");
echo "Price of BNB: {$price} BTC.\n";

while(true)
{
    $api->openOrders("BNBBTC");
}