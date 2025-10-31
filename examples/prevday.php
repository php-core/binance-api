<?php

require '../binance-api.php';

// use config from ~/.confg/php-core/binance-api.json
$api = new PHPCore\BinanceApi\BinanceApi();

// Getting 24hr ticker price change statistics for a symbol
$prevDay = $api->prevDay("BNBBTC");
print_r($prevDay);
echo "BNB price change since yesterday: ".$prevDay['priceChangePercent']."%".PHP_EOL;

// Getting 24hr ticker price change statistics for all symbols
$prevDay = $api->prevDay();
print_r($prevDay);