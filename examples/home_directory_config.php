<?php

require '../binance-api.php';

/*
mkdir -vp ~/.config/php-core/
cat >  ~/.config/php-core/binance-api.json << EOF
{
    "api-key": "<api key>",
    "api-secret": "<secret>"
}
*/

$api = new PHPCore\BinanceApi\BinanceApi();

$tickers = $api->prices();
print_r($tickers); // List prices of all symbols
