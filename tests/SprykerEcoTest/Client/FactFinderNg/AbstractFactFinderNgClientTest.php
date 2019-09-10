<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Client\FactFinderNg;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CartOrCheckoutEventTransfer;
use Generated\Shared\Transfer\ClickEventTransfer;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use SprykerEco\Client\FactFinderNg\Api\Adapter\FactFinderNgAdapterInterface;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactory;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface;
use SprykerEco\Client\FactFinderNg\FactFinderNgClient;
use SprykerEco\Client\FactFinderNg\FactFinderNgClientInterface;
use SprykerEco\Client\FactFinderNg\FactFinderNgConfig;
use SprykerEco\Client\FactFinderNg\FactFinderNgFactory;
use SprykerEco\Client\FactFinderNg\Handler\FactFinderHandler;

class AbstractFactFinderNgClientTest extends Unit
{
    protected const KEY_TOTAL_HITS = 'totalHits';
    protected const KEY_PAGE = 'page';
    protected const KEY_IPP = 'ipp';

    /**
     * @return \SprykerEco\Client\FactFinderNg\FactFinderNgClientInterface
     */
    protected function prepareClient(): FactFinderNgClientInterface
    {
        $client = new FactFinderNgClient();
        $client->setFactory($this->prepareFactory());

        return $client;
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\FactFinderNgFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function prepareFactory(): FactFinderNgFactory
    {
        $factory = $this->getMockBuilder(FactFinderNgFactory::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'createAdapterFactory',
                'getConfig',
                'getProductStorageClient',
                'getProductImageStorageClient',
                'getPriceProductStorageClient',
                'getLocaleClient',
                'getStoreClient',
                'getUtilEncodingService',
                'createSearchHandler',
                'createSuggestHandler',
                'createNavigationHandler',
            ])
            ->getMock();

        $factory->method('createAdapterFactory')->willReturn($this->createAdapterFactoryMock());
        $factory->method('getConfig')->willReturn($this->getFactFinderNgConfig());
        $factory->method('createSearchHandler')->willReturn($this->getFactFinderHandlerMock());
        $factory->method('createSuggestHandler')->willReturn($this->getFactFinderHandlerMock());
        $factory->method('createNavigationHandler')->willReturn($this->getFactFinderHandlerMock());

        return $factory;
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\FactFinderNgConfig
     */
    protected function getFactFinderNgConfig(): FactFinderNgConfig
    {
        return new FactFinderNgConfig();
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createAdapterFactoryMock(): AdapterFactoryInterface
    {
        $adapterFactory = $this->getMockBuilder(AdapterFactory::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'createFactFinderNgTrackCheckoutApiAdapter',
                'createFactFinderNgTrackCartApiAdapter',
                'createFactFinderNgTrackClickApiAdapter',
                'createFactFinderImportSearchAdapter',
            ])
            ->getMock();

        $adapterFactory->method('createFactFinderNgTrackCheckoutApiAdapter')->willReturn($this->prepareAdapter());
        $adapterFactory->method('createFactFinderNgTrackCartApiAdapter')->willReturn($this->prepareAdapter());
        $adapterFactory->method('createFactFinderNgTrackClickApiAdapter')->willReturn($this->prepareAdapter());
        $adapterFactory->method('createFactFinderImportSearchAdapter')->willReturn($this->prepareAdapter());

        return $adapterFactory;
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Api\Adapter\FactFinderNgAdapterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function prepareAdapter(): FactFinderNgAdapterInterface
    {
        $adapter = $this->getMockBuilder(FactFinderNgAdapterInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'sendRequest',
            ])
            ->getMock();

        $adapter->method('sendRequest')->willReturn($this->prepareSearchAdapterResponse());

        return $adapter;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function prepareSearchAdapterResponse(): ResponseInterface
    {
        return new Response();
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Handler\FactFinderHandler|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getFactFinderHandlerMock(): FactFinderHandler
    {
        $handler = $this->getMockBuilder(FactFinderHandler::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'search',
                'executeQuery',
            ])
            ->getMock();

        $handler->method('search')->willReturn($this->getSearchResult());

        return $handler;
    }

    /**
     * @return array
     */
    protected function getSearchResult(): array
    {
        return [
            static::KEY_TOTAL_HITS => 12,
            static::KEY_PAGE => 1,
            static::KEY_IPP => 12,
        ];
    }

    /**
     * @return array
     */
    protected function prepareCartOrCheckoutEventTransfers(): array
    {
        $event = new CartOrCheckoutEventTransfer();
        $event->setPrice(100);
        $event->setSid(uniqid());
        $event->setId('sku1');
        $event->setMasterId('master_sku1');
        $event->setCampaign('campaign1');
        $event->setUserId(1);
        $event->setTitle('title1');
        $event->setCount(1);

        $events[] = $event;

        $event = new CartOrCheckoutEventTransfer();
        $event->setPrice(100);
        $event->setSid(uniqid());
        $event->setId('sku2');
        $event->setMasterId('master_sku2');
        $event->setCampaign('campaign2');
        $event->setUserId(2);
        $event->setTitle('title2');
        $event->setCount(2);

        $events[] = $event;

        return $events;
    }

    /**
     * @return array
     */
    protected function prepareClickEventTransfers(): array
    {
        $event = new ClickEventTransfer();
        $event->setSid(uniqid());
        $event->setId('sku1');
        $event->setMasterId('master_sku1');
        $event->setCampaign('campaign1');
        $event->setUserId(1);
        $event->setTitle('title1');

        $events[] = $event;

        $event = new ClickEventTransfer();
        $event->setSid(uniqid());
        $event->setId('sku2');
        $event->setMasterId('master_sku2');
        $event->setCampaign('campaign2');
        $event->setUserId(2);
        $event->setTitle('title2');

        $events[] = $event;

        return $events;
    }
}
