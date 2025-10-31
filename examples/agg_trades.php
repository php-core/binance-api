<?php

require '../binance-api.php';

// @see home_directory_config.php
// use config from ~/.confg/php-core/binance-api.json
$api = new PHPCore\BinanceApi\BinanceApi();

// Aggregate Trades List
$trades = $api->aggTrades("BNBBTC");
print_r($trades);
