<?php

namespace PHPCore\BinanceApi;

use PHPCore\BinanceApi\Exceptions\BinanceApiException;

class BinanceApi extends BaseApi
{
    protected $base;
    protected $wapi;
    protected $sapi;
    protected $dapi;
    
    private bool $isTestnet = false;
    private int $maxRetries = 3;

    public function __construct($api_key = '', $api_secret = '', $testnet = false)
    {
        $this->isTestnet = $testnet;
        
        if ($testnet) {
            // Use testnet endpoints
            $this->base = 'https://testnet.binance.vision/api/';
            $this->wapi = 'https://testnet.binance.vision/wapi/';
            $this->sapi = 'https://testnet.binance.vision/sapi/';
            $this->dapi = 'https://testnet.binance.vision/dapi/';
        } else {
            // Check if proxy is configured and available
            $proxyHost = $_ENV['BINANCE_PROXY_HOST'] ?? null;
            $proxyPort = $_ENV['BINANCE_PROXY_PORT'] ?? null;
            $proxyProtocol = $_ENV['BINANCE_PROXY_PROTOCOL'] ?? 'https';
            
            if ($proxyHost && $proxyPort) {
                // Use proxy if configured
                $baseUrl = $proxyProtocol . '://' . $proxyHost . ':' . $proxyPort;
                $this->base = $baseUrl . '/api/';
                $this->wapi = $baseUrl . '/wapi/';
                $this->sapi = $baseUrl . '/sapi/';
                $this->dapi = $baseUrl . '/dapi/';
            } else {
                // Fall back to direct Binance API
                $this->base = 'https://api.binance.com/api/';
                $this->wapi = 'https://api.binance.com/wapi/';
                $this->sapi = 'https://api.binance.com/sapi/';
                $this->dapi = 'https://dapi.binance.com/dapi/';
            }
        }
        
        parent::__construct($api_key, $api_secret);
    }

    /**
     * Override downloadCurlCaBundle to use a custom path if needed
     * By default, uses the parent implementation
     */
    protected function downloadCurlCaBundle()
    {
        // If you need a custom path, you can override this method in your implementation
        // For now, use the parent implementation
        parent::downloadCurlCaBundle();
    }

    /**
     * Check if an error should trigger a retry
     */
    private function shouldRetryError(string $errorMessage): bool {
        $retryableErrors = [
            'Timestamp for this request is outside of the recvWindow',
            'Connection timed out',
            'Could not resolve host',
            'Failed to connect',
            'Operation timed out',
            'Server returned nothing',
            'Empty reply from server'
        ];
        
        foreach ($retryableErrors as $retryableError) {
            if (strpos($errorMessage, $retryableError) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Override balances method to add specific error handling
     */
    public function balances(string $market_type = 'spot', array $params = [], string $api_version = 'v3') {
        try {
            return parent::balances($market_type, $params, $api_version);
        } catch (\Exception $e) {
            // Log the error for debugging
            error_log("BinanceAPI balances error: " . $e->getMessage());
            
            // If it's a timestamp error, it should have been retried already
            // Return empty array as fallback to prevent crashes
            if (strpos($e->getMessage(), 'Timestamp for this request is outside of the recvWindow') !== false) {
                error_log("Timestamp error persisted after retries - returning empty balances");
                return [];
            }
            
            throw $e;
        }
    }

    /**
     * Override time method to add fallback and better error handling
     */
    public function time(array $params = []) {
        try {
            $result = parent::time($params);
            
            // Check if result is valid
            if ($result === null || $result === false) {
                error_log("BinanceAPI time() returned null/false - proxy might be down");
                return ['serverTime' => time() * 1000]; // Fallback to local time in milliseconds
            }
            
            // If result is numeric (some versions return just timestamp), wrap it
            if (is_numeric($result)) {
                return ['serverTime' => $result];
            }
            
            // If it's an array with serverTime, return as-is
            if (is_array($result) && isset($result['serverTime'])) {
                return $result;
            }
            
            // If it's an array but missing serverTime, try to fix
            if (is_array($result)) {
                error_log("BinanceAPI time() returned array without serverTime: " . json_encode($result));
                return ['serverTime' => time() * 1000]; // Fallback
            }
            
            // Unknown format, log and fallback
            error_log("BinanceAPI time() returned unexpected format: " . var_export($result, true));
            return ['serverTime' => time() * 1000]; // Fallback to local time
            
        } catch (\Exception $e) {
            error_log("BinanceAPI time() exception: " . $e->getMessage());
            // Return local time as fallback
            return ['serverTime' => time() * 1000];
        }
    }

    /**
     * Override openOrders method to handle null responses gracefully
     */
    public function openOrders($symbol = null, array $params = []): array {
        try {
            $result = parent::openOrders($symbol, $params);
            
            // Check if result is valid
            if ($result === null || $result === false) {
                error_log("BinanceAPI openOrders() returned null/false - API might be down or rate limited");
                return []; // Return empty array instead of null
            }
            
            // If it's already an array, return as-is
            if (is_array($result)) {
                return $result;
            }
            
            // Unknown format, log and return empty array
            error_log("BinanceAPI openOrders() returned unexpected format: " . var_export($result, true));
            return [];
            
        } catch (\Exception $e) {
            error_log("BinanceAPI openOrders() exception: " . $e->getMessage());
            // Return empty array as fallback
            return [];
        }
    }

    /**
     * @return string
     */
    public function getBase(): string
    {
        return $this->base;
    }

    /**
     * @return string
     */
    public function getWapi(): string
    {
        return $this->wapi;
    }

    /**
     * @return string
     */
    public function getSapi(): string
    {
        return $this->sapi;
    }

    /**
     * @return string
     */
    public function getDapi(): string
    {
        return $this->dapi;
    }
    
    /**
     * @return bool
     */
    public function isTestnet(): bool
    {
        return $this->isTestnet;
    }





    /**
     * Simple Earn Account (GET /sapi/v1/simple-earn/account)
     */
    public function simpleEarnAccount(array $params = []): array
    {
        $result = $this->sapiRequest('v1/simple-earn/account', 'GET', $params, true);
        return $result ?? [];
    }

    /**
     * Simple Earn Flexible Product List (GET /sapi/v1/simple-earn/flexible/list)
     */
    public function simpleEarnFlexibleProductList(array $params = []): array
    {
        $result = $this->sapiRequest('v1/simple-earn/flexible/list', 'GET', $params, true);
        return $result ?? [];
    }

    /**
     * Simple Earn Locked Product List (GET /sapi/v1/simple-earn/locked/list)
     */
    public function simpleEarnLockedProductList(array $params = []): array
    {
        $result = $this->sapiRequest('v1/simple-earn/locked/list', 'GET', $params, true);
        return $result ?? [];
    }

    /**
     * Simple Earn Flexible Product Position (GET /sapi/v1/simple-earn/flexible/position)
     */
    public function simpleEarnFlexibleProductPosition(array $params = []): array
    {
        $result = $this->sapiRequest('v1/simple-earn/flexible/position', 'GET', $params, true);
        return $result ?? [];
    }

    /**
     * Simple Earn Locked Product Position (GET /sapi/v1/simple-earn/locked/position)
     */
    public function simpleEarnLockedProductPosition(array $params = []): array
    {
        $result = $this->sapiRequest('v1/simple-earn/locked/position', 'GET', $params, true);
        return $result ?? [];
    }

    /**
     * Simple Earn Flexible Subscribe (POST /sapi/v1/simple-earn/flexible/subscribe)
     */
    public function simpleEarnFlexibleSubscribe(array $params): array
    {
        $result = $this->sapiRequest('v1/simple-earn/flexible/subscribe', 'POST', $params, true);
        return $result ?? [];
    }

    /**
     * Simple Earn Flexible Redeem (POST /sapi/v1/simple-earn/flexible/redeem)
     */
    public function simpleEarnFlexibleRedeem(array $params): array
    {
        $result = $this->sapiRequest('v1/simple-earn/flexible/redeem', 'POST', $params, true);
        return $result ?? [];
    }

    /**
     * Simple Earn Locked Subscribe (POST /sapi/v1/simple-earn/locked/subscribe)
     */
    public function simpleEarnLockedSubscribe(array $params): array
    {
        $result = $this->sapiRequest('v1/simple-earn/locked/subscribe', 'POST', $params, true);
        return $result ?? [];
    }

    /**
     * @throws BinanceApiException
     * @throws \Throwable
     */
    public static function handleException(\Throwable $e): void
    {
        if (
            str_starts_with($e->getMessage(), 'signedRequest error: ')
            && !empty($message = trim(str_replace('signedRequest error: ', '', $e->getMessage())))
        ) {
            throw new BinanceApiException(
                empty($json = json_decode($message))
                || empty($json->msg)
                    ? $message
                    : $json->msg
            );
        }
        throw $e;
    }

    /**
     * Get a quote for converting one asset to another
     *
     * @param string $fromAsset The asset to convert from (e.g., "USDT")
     * @param string $toAsset The asset to convert to (e.g., "BTC")
     * @param float|string $fromAmount The amount of fromAsset to convert (optional if toAmount is provided)
     * @param float|string|null $toAmount The amount of toAsset to receive (optional if fromAmount is provided)
     * @return array Quote details including quoteId, ratio, inverseRatio, validTimestamp, toAmount, fromAmount
     * @throws \Exception
     */
    public function getConvertQuote(string $fromAsset, string $toAsset, $fromAmount = null, $toAmount = null): array
    {
        if ($fromAmount === null && $toAmount === null) {
            throw new \Exception("Either fromAmount or toAmount must be specified");
        }

        if ($fromAmount !== null && $toAmount !== null) {
            throw new \Exception("Only one of fromAmount or toAmount should be specified, not both");
        }

        $params = [
            'fromAsset' => $fromAsset,
            'toAsset' => $toAsset,
        ];

        if ($fromAmount !== null) {
            $params['fromAmount'] = $fromAmount;
        } else {
            $params['toAmount'] = $toAmount;
        }

        $result = $this->sapiRequest('v1/convert/getQuote', 'POST', $params, true);
        return $result ?? [];
    }

    /**
     * Accept a convert quote and execute the conversion
     *
     * @param string $quoteId The quote ID from getConvertQuote
     * @return array Conversion result including orderId, createTime, orderStatus
     * @throws \Exception
     */
    public function acceptConvertQuote(string $quoteId): array
    {
        $params = ['quoteId' => $quoteId];
        $result = $this->sapiRequest('v1/convert/acceptQuote', 'POST', $params, true);
        return $result ?? [];
    }

    /**
     * Convert one asset to another (convenience method that gets quote and accepts it)
     *
     * This method combines getConvertQuote and acceptConvertQuote for easier usage.
     *
     * Example:
     * $result = $api->convertAsset("USDT", "BTC", 100); // Convert 100 USDT to BTC
     *
     * @param string $fromAsset The asset to convert from (e.g., "USDT")
     * @param string $toAsset The asset to convert to (e.g., "BTC")
     * @param float|string $fromAmount The amount of fromAsset to convert (optional if toAmount is provided)
     * @param float|string|null $toAmount The amount of toAsset to receive (optional if fromAmount is provided)
     * @return array Conversion result with both quote and execution details
     * @throws \Exception
     */
    public function convertAsset(string $fromAsset, string $toAsset, $fromAmount = null, $toAmount = null): array
    {
        // Get quote
        $quote = $this->getConvertQuote($fromAsset, $toAsset, $fromAmount, $toAmount);

        if (!isset($quote['quoteId'])) {
            throw new \Exception("Failed to get quote: " . json_encode($quote));
        }

        // Accept quote immediately
        $result = $this->acceptConvertQuote($quote['quoteId']);

        // Return combined information
        return array_merge($quote, $result);
    }

    /**
     * Query convert order status
     *
     * @param string|null $orderId Order ID to query (optional if quoteId is provided)
     * @param string|null $quoteId Quote ID to query (optional if orderId is provided)
     * @return array Order status details
     * @throws \Exception
     */
    public function getConvertOrderStatus(?string $orderId = null, ?string $quoteId = null): array
    {
        if ($orderId === null && $quoteId === null) {
            throw new \Exception("Either orderId or quoteId must be specified");
        }

        $params = [];
        if ($orderId !== null) {
            $params['orderId'] = $orderId;
        }
        if ($quoteId !== null) {
            $params['quoteId'] = $quoteId;
        }

        $result = $this->sapiRequest('v1/convert/orderStatus', 'GET', $params, true);
        return $result ?? [];
    }

    /**
     * Get convert trade flow (history)
     *
     * @param int $startTime Start time in milliseconds (required)
     * @param int $endTime End time in milliseconds (required)
     * @param int $limit Number of records to return (default 100, max 1000)
     * @return array List of convert trades
     * @throws \Exception
     */
    public function getConvertTradeFlow(int $startTime, int $endTime, int $limit = 100): array
    {
        $params = [
            'startTime' => $startTime,
            'endTime' => $endTime,
            'limit' => $limit,
        ];

        $result = $this->sapiRequest('v1/convert/tradeFlow', 'GET', $params, true);
        return $result ?? [];
    }
}