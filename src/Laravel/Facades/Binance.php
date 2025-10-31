<?php

namespace PHPCore\BinanceApi\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array prices()
 * @method static array price(string $symbol)
 * @method static array bookPrices(string $symbol = null)
 * @method static array account(array $params = [])
 * @method static array balances(string $market_type = 'spot', array $params = [], string $api_version = 'v3')
 * @method static array depth(string $symbol, int $limit = 100)
 * @method static array ticker(string $symbol = null, array $params = [])
 * @method static array trades(string $symbol, int $limit = 500)
 * @method static array aggTrades(string $symbol, array $params = [])
 * @method static array historicalTrades(string $symbol, array $params = [])
 * @method static array candlesticks(string $symbol, string $interval, array $params = [])
 * @method static array buy(string $symbol, float $quantity, float $price, string $type = 'LIMIT', array $flags = [])
 * @method static array sell(string $symbol, float $quantity, float $price, string $type = 'LIMIT', array $flags = [])
 * @method static array marketBuy(string $symbol, float $quantity, array $flags = [])
 * @method static array marketSell(string $symbol, float $quantity, array $flags = [])
 * @method static array cancel(string $symbol, int $orderId, array $flags = [])
 * @method static array orderStatus(string $symbol, int $orderId, array $flags = [])
 * @method static array openOrders(string $symbol = null, array $params = [])
 * @method static array orders(string $symbol, array $params = [])
 * @method static array myTrades(string $symbol, array $params = [])
 * @method static array time(array $params = [])
 * @method static array exchangeInfo()
 * @method static array withdraw(string $asset, string $address, float $amount, array $params = [])
 * @method static array depositAddress(string $asset, array $params = [])
 * @method static array depositHistory(array $params = [])
 * @method static array withdrawHistory(array $params = [])
 * @method static array assetDetail(array $params = [])
 * @method static array tradeFee(array $params = [])
 * @method static array simpleEarnAccount(array $params = [])
 * @method static array simpleEarnFlexibleProductList(array $params = [])
 * @method static array simpleEarnLockedProductList(array $params = [])
 * @method static array simpleEarnFlexibleProductPosition(array $params = [])
 * @method static array simpleEarnLockedProductPosition(array $params = [])
 * @method static array simpleEarnFlexibleSubscribe(array $params)
 * @method static array simpleEarnFlexibleRedeem(array $params)
 * @method static array simpleEarnLockedSubscribe(array $params)
 * @method static array getConvertQuote(string $fromAsset, string $toAsset, $fromAmount = null, $toAmount = null)
 * @method static array acceptConvertQuote(string $quoteId)
 * @method static array convertAsset(string $fromAsset, string $toAsset, $fromAmount = null, $toAmount = null)
 * @method static array getConvertOrderStatus(string $orderId = null, string $quoteId = null)
 * @method static array getConvertTradeFlow(int $startTime, int $endTime, int $limit = 100)
 * @method static string getBase()
 * @method static string getWapi()
 * @method static string getSapi()
 * @method static string getDapi()
 * @method static bool isTestnet()
 *
 * @see \PHPCore\BinanceApi\BinanceApi
 */
class Binance extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'binance';
    }
}
