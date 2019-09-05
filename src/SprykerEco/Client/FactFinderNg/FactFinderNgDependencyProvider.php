<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg;

use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;
use SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceBridge;

class FactFinderNgDependencyProvider extends AbstractDependencyProvider
{
    public const SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';
    public const CLIENT_PRODUCT_STORAGE = 'CLIENT_PRODUCT_STORAGE';
    public const CLIENT_PRODUCT_IMAGE_STORAGE = 'CLIENT_PRODUCT_IMAGE_STORAGE';
    public const CLIENT_PRICE_PRODUCT_STORAGE = 'CLIENT_PRICE_PRODUCT_STORAGE';
    public const CLIENT_LOCALE = 'CLIENT_LOCALE';
    public const CLIENT_STORE = 'CLIENT_STORE';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container): Container
    {
        $container = $this->addUtilEncodingService($container);
        $container = $this->addProductStorageClient($container);
        $container = $this->addProductImageStorageClient($container);
        $container = $this->addPriceProductStorage($container);
        $container = $this->addLocaleClient($container);
        $container = $this->addStoreClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addUtilEncodingService(Container $container): Container
    {
        $container[static::SERVICE_UTIL_ENCODING] = function (Container $container) {
            return new FactFinderNgToUtilEncodingServiceBridge($container->getLocator()->utilEncoding()->service());
        };

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addProductStorageClient(Container $container): Container
    {
        $container[static::CLIENT_PRODUCT_STORAGE] = function (Container $container) {
            return $container->getLocator()->productStorage()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addProductImageStorageClient(Container $container): Container
    {
        $container[static::CLIENT_PRODUCT_IMAGE_STORAGE] = function (Container $container) {
            return $container->getLocator()->productImageStorage()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addPriceProductStorage(Container $container): Container
    {
        $container[static::CLIENT_PRICE_PRODUCT_STORAGE] = function (Container $container) {
            return $container->getLocator()->priceProductStorage()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addLocaleClient(Container $container): Container
    {
        $container[static::CLIENT_LOCALE] = function (Container $container) {
            return $container->getLocator()->locale()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addStoreClient(Container $container): Container
    {
        $container[static::CLIENT_STORE] = function (Container $container) {
            return $container->getLocator()->store()->client();
        };

        return $container;
    }
}
