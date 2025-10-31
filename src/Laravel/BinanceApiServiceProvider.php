<?php

namespace PHPCore\BinanceApi\Laravel;

use Illuminate\Support\ServiceProvider;
use PHPCore\BinanceApi\BinanceApi;

class BinanceApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/binance.php',
            'binance'
        );

        $this->app->singleton('binance', function ($app) {
            $config = $app['config']['binance'];

            return new BinanceApi(
                $config['api_key'] ?? '',
                $config['api_secret'] ?? '',
                $config['testnet'] ?? false
            );
        });

        $this->app->alias('binance', BinanceApi::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/binance.php' => config_path('binance.php'),
            ], 'binance-config');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['binance', BinanceApi::class];
    }
}
