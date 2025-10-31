<?php

require '../binance-api.php';

// @see home_directory_config.php
// use config from ~/.confg/php-core/binance-api.json
$api = new PHPCore\BinanceApi\BinanceApi();

// Get all bid/ask prices
$bookPrices = $api->bookPrices();
print_r($bookPrices);
