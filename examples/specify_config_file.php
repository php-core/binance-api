<?php

require '../binance-api.php';

// @see home_directory_config.php
// use config from ~/.confg/php-core/binance-api.json
$api = new PHPCore\BinanceApi\BinanceApi( getenv( "HOME" ) . "/.config/php-core/binance-api.json" );

$account = $api->account();
print_r($account); 
