<?php

require 'php-binance-api.php';
require 'vendor/autoload.php';


// @see home_directory_config.php
// use config from ~/.confg/php-core/binance-api.json
$api = new PHPCore\BinanceApi\BinanceApi();

// Trade Updates via WebSocket
$api->kline(["BTCUSDT", "EOSBTC"], "5m", function($api, $symbol, $chart) {
    var_dump( $chart );
    //echo "{$symbol} ({$interval}) candlestick update\n";
    $interval = $chart->i;
    $tick = $chart->t;
    $open = $chart->o;
    $high = $chart->h;
    $low = $chart->l;
    $close = $chart->c;
    $volume = $chart->q; // +trades buyVolume assetVolume makerVolume
    echo "{$symbol} price: {$close}\t volume: {$volume}\n";

    $endpoint = strtolower( $symbol ) . '@kline_' . "5m";
    $api->terminate( $endpoint );
});