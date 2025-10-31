<?php

require 'php-binance-api.php';
require 'vendor/autoload.php';

// @see home_directory_config.php
// use config from ~/.confg/php-core/binance-api.json
$api = new PHPCore\BinanceApi\BinanceApi();

// Get complete realtime chart data via WebSockets
$api->chart(["BNBBTC"], "15m", function($api, $symbol, $chart) {
    echo "{$symbol} chart update\n";
    print_r($chart);
    $endpoint = strtolower( $symbol ) . '@kline_' . "15m";
    $api->terminate( $endpoint );
});
