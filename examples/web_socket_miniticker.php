<?php

require 'php-binance-api.php';
require 'vendor/autoload.php';


// @see home_directory_config.php
// use config from ~/.confg/php-core/binance-api.json
$api = new PHPCore\BinanceApi\BinanceApi();

$count = 0;

$api->miniTicker( function ( $api, $ticker ) use ( &$count ) {
   print_r( $ticker );
   $count++;
   print $count . "\n";
   if($count > 2) {
      $endpoint = '@miniticker';
      $api->terminate( $endpoint );
   }
} );