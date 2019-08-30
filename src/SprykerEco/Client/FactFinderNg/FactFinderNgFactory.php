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
     * @return \SprykerEco\Client\FactFinderNg\Api\RequestSender\RequestSenderInterface
     */
    public function createRequestSender(): RequestSenderInterface
    {
        return new RequestSender(
            $this->createRequestMapper(),
            $this->createResponseParser(),
            $this->createAdapterFactory()
        );
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
            $this->createFactFinderNgSearchToElasticaMapper(),
            $this->createRequestSender(),
            $this->getLocaleClient(),
            $this->getStoreClient()
        );
    }

    /**
     * @return \Spryker\Client\Search\Model\Handler\SearchHandlerInterface
     */
    public function createSuggestHandler(): SearchHandlerInterface
    {
        return new FactFinderNgSuggestHandler(
            $this->createFactFinderNgSuggestToElasticaMapper(),
            $this->createRequestSender(),
            $this->getLocaleClient(),
            $this->getStoreClient()
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
     * @return TrackApiRequestMapperInterface
     */
    public function createTrackApiRequestMapper(): TrackApiRequestMapperInterface
    {
        return new TrackApiRequestMapper();
    }

    /**
     * @return TrackCheckoutProcessor
     */
    public function createTrackCheckoutProcessor(): TrackCheckoutProcessor
    {
        return new TrackCheckoutProcessor(
            $this->createTrackApiRequestMapper(),
            $this->createAdapterFactory(),
            $this->createResponseParser()
        );
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
}
