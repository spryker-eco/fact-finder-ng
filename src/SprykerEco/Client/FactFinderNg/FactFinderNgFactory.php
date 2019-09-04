<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg;

use Elastica\ResultSet\DefaultBuilder;
use GuzzleHttp\ClientInterface;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactory;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface;
use SprykerEco\Client\FactFinderNg\Api\Client\FactFinderNgHttpClient;
use SprykerEco\Client\FactFinderNg\Api\RequestSender\RequestSender;
use SprykerEco\Client\FactFinderNg\Api\RequestSender\RequestSenderInterface;
use SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface;
use SprykerEco\Client\FactFinderNg\EventTracker\CartEventTracker;
use SprykerEco\Client\FactFinderNg\EventTracker\CheckoutEventTracker;
use SprykerEco\Client\FactFinderNg\EventTracker\ClickEventTracker;
use SprykerEco\Client\FactFinderNg\EventTracker\EventTrackerInterface;
use SprykerEco\Client\FactFinderNg\Handler\FactFinderNavigationHandler;
use SprykerEco\Client\FactFinderNg\Handler\FactFinderNgSearchHandler;
use SprykerEco\Client\FactFinderNg\Handler\FactFinderNgSuggestHandler;
use SprykerEco\Client\FactFinderNg\ImportTrigger\ImportTriggerInterface;
use SprykerEco\Client\FactFinderNg\ImportTrigger\SearchImportTrigger;
use SprykerEco\Client\FactFinderNg\Mapper\Elastica\FactFinderNgSearchToElasticaMapper;
use SprykerEco\Client\FactFinderNg\Mapper\Elastica\FactFinderNgSuggestToElasticaMapper;
use SprykerEco\Client\FactFinderNg\Mapper\Elastica\FactFinderToElasticaMapperInterface;
use SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapper;
use SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface;
use SprykerEco\Client\FactFinderNg\Mapper\Request\Track\TrackApiRequestMapper;
use SprykerEco\Client\FactFinderNg\Mapper\Request\Track\TrackApiRequestMapperInterface;
use SprykerEco\Client\FactFinderNg\Parser\ResponseParser;
use SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\Locale\LocaleClientInterface;
use Spryker\Client\PriceProductStorage\PriceProductStorageClientInterface;
use Spryker\Client\ProductImageStorage\ProductImageStorageClientInterface;
use Spryker\Client\ProductStorage\ProductStorageClientInterface;
use Spryker\Client\Search\Model\Handler\SearchHandlerInterface;
use Spryker\Client\Store\StoreClientInterface;
use SprykerEco\Client\FactFinderNg\Processor\TrackCheckoutProcessor;

/**
 * @method \SprykerEco\Client\FactFinderNg\FactFinderNgConfig getConfig()
 */
class FactFinderNgFactory extends AbstractFactory
{
    /**
     * @return \SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface
     */
    public function createAdapterFactory(): AdapterFactoryInterface
    {
        return new AdapterFactory(
            $this->createFactFinderNgClient(),
            $this->getUtilEncodingService(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): FactFinderNgToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(FactFinderNgDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \GuzzleHttp\ClientInterface
     */
    public function createFactFinderNgClient(): ClientInterface
    {
        return new FactFinderNgHttpClient();
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface
     */
    public function createRequestMapper(): FactFinderNgRequestMapperInterface
    {
        return new FactFinderNgRequestMapper($this->getConfig());
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface
     */
    public function createResponseParser(): ResponseParserInterface
    {
        return new ResponseParser(
            $this->getUtilEncodingService()
        );
    }

    /**
     * @return \Spryker\Client\Search\Model\Handler\SearchHandlerInterface
     */
    public function createSearchHandler(): SearchHandlerInterface
    {
        return new FactFinderNgSearchHandler(
            $this->createRequestMapper(),
            $this->createAdapterFactory(),
            $this->createResponseParser(),
            $this->createFactFinderNgSearchToElasticaMapper(),
            $this->getLocaleClient(),
            $this->getStoreClient(),
            $this->getUtilEncodingService()
        );
    }

    /**
     * @return \Spryker\Client\Search\Model\Handler\SearchHandlerInterface
     */
    public function createSuggestHandler(): SearchHandlerInterface
    {
        return new FactFinderNgSuggestHandler(
            $this->createRequestMapper(),
            $this->createAdapterFactory(),
            $this->createResponseParser(),
            $this->createFactFinderNgSuggestToElasticaMapper(),
            $this->getLocaleClient(),
            $this->getStoreClient(),
            $this->getUtilEncodingService()
        );
    }

    /**
     * @return SearchHandlerInterface
     */
    public function createNavigationHandler(): SearchHandlerInterface
    {
        return new FactFinderNavigationHandler(
            $this->createRequestMapper(),
            $this->createAdapterFactory(),
            $this->createResponseParser(),
            $this->createFactFinderNgSearchToElasticaMapper(),
            $this->getLocaleClient(),
            $this->getStoreClient(),
            $this->getUtilEncodingService()
        );
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Mapper\Elastica\FactFinderToElasticaMapperInterface
     */
    public function createFactFinderNgSearchToElasticaMapper(): FactFinderToElasticaMapperInterface
    {
        return new FactFinderNgSearchToElasticaMapper(
            $this->createElasticaDefaultBuilder(),
            $this->getProductStorageClient(),
            $this->getProductImageStorageClient(),
            $this->getPriceProductStorageClient()
        );
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Mapper\Elastica\FactFinderToElasticaMapperInterface
     */
    public function createFactFinderNgSuggestToElasticaMapper(): FactFinderToElasticaMapperInterface
    {
        return new FactFinderNgSuggestToElasticaMapper(
            $this->createElasticaDefaultBuilder(),
            $this->getProductStorageClient(),
            $this->getProductImageStorageClient(),
            $this->getPriceProductStorageClient()
        );
    }

    /**
     * @return \Elastica\ResultSet\DefaultBuilder
     */
    public function createElasticaDefaultBuilder(): DefaultBuilder
    {
        return new DefaultBuilder();
    }

    /**
     * @return \Spryker\Client\ProductStorage\ProductStorageClientInterface
     */
    public function getProductStorageClient(): ProductStorageClientInterface
    {
        return $this->getProvidedDependency(FactFinderNgDependencyProvider::CLIENT_PRODUCT_STORAGE);
    }

    /**
     * @return \Spryker\Client\ProductImageStorage\ProductImageStorageClientInterface
     */
    public function getProductImageStorageClient(): ProductImageStorageClientInterface
    {
        return $this->getProvidedDependency(FactFinderNgDependencyProvider::CLIENT_PRODUCT_IMAGE_STORAGE);
    }

    /**
     * @return \Spryker\Client\PriceProductStorage\PriceProductStorageClientInterface
     */
    public function getPriceProductStorageClient(): PriceProductStorageClientInterface
    {
        return $this->getProvidedDependency(FactFinderNgDependencyProvider::CLIENT_PRICE_PRODUCT_STORAGE);
    }

    /**
     * @return \Spryker\Client\Locale\LocaleClientInterface
     */
    public function getLocaleClient(): LocaleClientInterface
    {
        return $this->getProvidedDependency(FactFinderNgDependencyProvider::CLIENT_LOCALE);
    }

    /**
     * @return \Spryker\Client\Store\StoreClientInterface
     */
    public function getStoreClient(): StoreClientInterface
    {
        return $this->getProvidedDependency(FactFinderNgDependencyProvider::CLIENT_STORE);
    }

    /**
     * @return ImportTriggerInterface
     */
    public function createSearchImportTrigger(): ImportTriggerInterface
    {
        return new SearchImportTrigger(
            $this->createRequestMapper(),
            $this->createResponseParser(),
            $this->createAdapterFactory()
        );
    }

    /**
     * @return EventTrackerInterface
     */
    public function createCheckoutEventTracker(): EventTrackerInterface
    {
        return new CheckoutEventTracker(
            $this->createRequestMapper(),
            $this->createAdapterFactory(),
            $this->createResponseParser()
        );
    }

    /**
     * @return EventTrackerInterface
     */
    public function createCartEventTracker(): EventTrackerInterface
    {
        return new CartEventTracker(
            $this->createRequestMapper(),
            $this->createAdapterFactory(),
            $this->createResponseParser()
        );
    }

    /**
     * @return EventTrackerInterface
     */
    public function createClickEventTracker(): EventTrackerInterface
    {
        return new ClickEventTracker(
            $this->createRequestMapper(),
            $this->createAdapterFactory(),
            $this->createResponseParser()
        );
    }
}
