<?php

use PHPUnit\Framework\TestCase;
use PHPCore\BinanceApi\BinanceApi;
use PHPCore\BinanceApi\Laravel\BinanceApiServiceProvider;
use PHPCore\BinanceApi\Laravel\Facades\Binance;

class LaravelIntegrationTest extends TestCase
{
    /**
     * Test that the service provider file exists
     */
    public function testServiceProviderFileExists()
    {
        $filePath = __DIR__ . '/../src/Laravel/BinanceApiServiceProvider.php';
        $this->assertFileExists($filePath, 'BinanceApiServiceProvider file should exist');
    }

    /**
     * Test that the facade file exists
     */
    public function testFacadeFileExists()
    {
        $filePath = __DIR__ . '/../src/Laravel/Facades/Binance.php';
        $this->assertFileExists($filePath, 'Binance facade file should exist');
    }

    /**
     * Test that facade extends Laravel Facade
     */
    public function testFacadeExtendsLaravelFacade()
    {
        // Only run this test if illuminate/support is available
        if (!class_exists('Illuminate\Support\Facades\Facade')) {
            $this->markTestSkipped('illuminate/support is not installed');
        }

        $this->assertTrue(
            is_subclass_of(Binance::class, 'Illuminate\Support\Facades\Facade'),
            'Binance facade should extend Illuminate\Support\Facades\Facade'
        );
    }

    /**
     * Test that service provider extends Laravel ServiceProvider
     */
    public function testServiceProviderExtendsLaravelServiceProvider()
    {
        // Only run this test if illuminate/support is available
        if (!class_exists('Illuminate\Support\ServiceProvider')) {
            $this->markTestSkipped('illuminate/support is not installed');
        }

        $this->assertTrue(
            is_subclass_of(BinanceApiServiceProvider::class, 'Illuminate\Support\ServiceProvider'),
            'BinanceApiServiceProvider should extend Illuminate\Support\ServiceProvider'
        );
    }

    /**
     * Test that config file exists and is valid PHP
     */
    public function testConfigFileExistsAndIsValid()
    {
        $configPath = __DIR__ . '/../config/binance.php';

        $this->assertFileExists($configPath, 'Config file should exist');

        $config = require $configPath;

        $this->assertIsArray($config, 'Config should return an array');
        $this->assertArrayHasKey('api_key', $config, 'Config should have api_key');
        $this->assertArrayHasKey('api_secret', $config, 'Config should have api_secret');
        $this->assertArrayHasKey('testnet', $config, 'Config should have testnet');
        $this->assertArrayHasKey('proxy', $config, 'Config should have proxy configuration');
    }

    /**
     * Test that service provider has correct provides method
     */
    public function testServiceProviderProvidesMethod()
    {
        // Only run this test if illuminate/support is available
        if (!class_exists('Illuminate\Support\ServiceProvider')) {
            $this->markTestSkipped('illuminate/support is not installed');
        }

        $app = $this->createMockApplication();
        $provider = new BinanceApiServiceProvider($app);

        $provides = $provider->provides();

        $this->assertIsArray($provides);
        $this->assertContains('binance', $provides);
        $this->assertContains(BinanceApi::class, $provides);
    }

    /**
     * Test that service provider registers binance singleton
     */
    public function testServiceProviderRegistersBinanceSingleton()
    {
        // Only run this test if illuminate/support is available
        if (!class_exists('Illuminate\Support\ServiceProvider')) {
            $this->markTestSkipped('illuminate/support is not installed');
        }

        $app = $this->createMockApplication();
        $provider = new BinanceApiServiceProvider($app);

        $provider->register();

        // Check that singleton was registered
        $this->assertTrue(
            isset($app->singletons['binance']),
            'Binance should be registered as singleton'
        );

        // Check that alias was registered
        $this->assertTrue(
            isset($app->aliases['binance']),
            'Binance alias should be registered'
        );

        $this->assertEquals(
            BinanceApi::class,
            $app->aliases['binance'],
            'Binance alias should point to BinanceApi class'
        );
    }

    /**
     * Test that the binance instance can be created from config
     */
    public function testBinanceInstanceCanBeCreatedFromConfig()
    {
        // Only run this test if illuminate/support is available
        if (!class_exists('Illuminate\Support\ServiceProvider')) {
            $this->markTestSkipped('illuminate/support is not installed');
        }

        $app = $this->createMockApplication([
            'binance' => [
                'api_key' => 'test_key',
                'api_secret' => 'test_secret',
                'testnet' => true,
            ]
        ]);

        $provider = new BinanceApiServiceProvider($app);
        $provider->register();

        // Resolve the binance instance
        $binance = $app->singletons['binance']($app);

        $this->assertInstanceOf(BinanceApi::class, $binance);
        $this->assertTrue($binance->isTestnet(), 'Binance should be in testnet mode');
    }

    /**
     * Test facade accessor returns correct name
     */
    public function testFacadeAccessorReturnsCorrectName()
    {
        // Only run this test if illuminate/support is available
        if (!class_exists('Illuminate\Support\Facades\Facade')) {
            $this->markTestSkipped('illuminate/support is not installed');
        }

        $reflection = new \ReflectionClass(Binance::class);
        $method = $reflection->getMethod('getFacadeAccessor');
        $method->setAccessible(true);

        $accessor = $method->invoke(null);

        $this->assertEquals('binance', $accessor);
    }

    /**
     * Create a mock Laravel application for testing
     */
    private function createMockApplication(array $config = [])
    {
        return new class($config) {
            public $singletons = [];
            public $aliases = [];
            private $config = [];
            private $runningInConsole = false;

            public function __construct(array $config = [])
            {
                $this->config = $config;
            }

            public function singleton($abstract, $concrete)
            {
                $this->singletons[$abstract] = $concrete;
            }

            public function alias($abstract, $alias)
            {
                $this->aliases[$abstract] = $alias;
            }

            public function offsetGet($key)
            {
                if ($key === 'config') {
                    return $this;
                }
                return null;
            }

            public function get($key, $default = null)
            {
                return $this->config[$key] ?? $default;
            }

            public function runningInConsole()
            {
                return $this->runningInConsole;
            }
        };
    }
}
