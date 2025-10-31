<?php

require '../binance-api.php';

// @see home_directory_config.php
// use config from ~/.confg/php-core/binance-api.json
$api = new PHPCore\BinanceApi\BinanceApi();

// Get all of your positions, including estimated BTC value
$balances = $api->balances();
print_r($balances);
