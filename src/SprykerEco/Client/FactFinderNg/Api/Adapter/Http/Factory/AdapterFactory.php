<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory;

use GuzzleHttp\ClientInterface;
use SprykerEco\Client\FactFinderNg\Api\Adapter\FactFinderNgAdapterInterface;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\FactFinderNgImportSearchAdapter;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\FactFinderNgNavigationAdapter;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\FactFinderNgSearchAdapter;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\FactFinderNgSuggestAdapter;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\FactFinderNgTrackCartAdapter;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\FactFinderNgTrackCheckoutAdapter;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\FactFinderNgTrackClickAdapter;
use SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface;
use SprykerEco\Client\FactFinderNg\FactFinderNgConfig;

class AdapterFactory implements AdapterFactoryInterface
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    /**
     * @var \SprykerEco\Client\FactFinderNg\FactFinderNgConfig
     */
    protected $config;

    /**
     * @param \GuzzleHttp\ClientInterface $client
     * @param \SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface $utilEncodingService
     * @param \SprykerEco\Client\FactFinderNg\FactFinderNgConfig $config
     */
    public function __construct(
        ClientInterface $client,
        FactFinderNgToUtilEncodingServiceInterface $utilEncodingService,
        FactFinderNgConfig $config
    ) {
        $this->config = $config;
        $this->client = $client;
        $this->utilEncodingService = $utilEncodingService;
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Api\Adapter\FactFinderNgAdapterInterface
     */
    public function createFactFinderNgSearchAdapter(): FactFinderNgAdapterInterface
    {
        return new FactFinderNgSearchAdapter(
            $this->client,
            $this->utilEncodingService,
            $this->config
        );
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Api\Adapter\FactFinderNgAdapterInterface
     */
    public function createFactFinderNgSuggestionAdapter(): FactFinderNgAdapterInterface
    {
        return new FactFinderNgSuggestAdapter(
            $this->client,
            $this->utilEncodingService,
            $this->config
        );
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Api\Adapter\FactFinderNgAdapterInterface
     */
    public function createFactFinderNgNavigationAdapter(): FactFinderNgAdapterInterface
    {
        return new FactFinderNgNavigationAdapter(
            $this->client,
            $this->utilEncodingService,
            $this->config
        );
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Api\Adapter\FactFinderNgAdapterInterface
     */
    public function createFactFinderImportSearchAdapter(): FactFinderNgAdapterInterface
    {
        return new FactFinderNgImportSearchAdapter(
            $this->client,
            $this->utilEncodingService,
            $this->config
        );
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Api\Adapter\FactFinderNgAdapterInterface
     */
    public function createFactFinderNgTrackCheckoutApiAdapter(): FactFinderNgAdapterInterface
    {
        return new FactFinderNgTrackCheckoutAdapter(
            $this->client,
            $this->utilEncodingService,
            $this->config
        );
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Api\Adapter\FactFinderNgAdapterInterface
     */
    public function createFactFinderNgTrackCartApiAdapter(): FactFinderNgAdapterInterface
    {
        return new FactFinderNgTrackCartAdapter(
            $this->client,
            $this->utilEncodingService,
            $this->config
        );
    }

    /**
     * @return \SprykerEco\Client\FactFinderNg\Api\Adapter\FactFinderNgAdapterInterface
     */
    public function createFactFinderNgTrackClickApiAdapter(): FactFinderNgAdapterInterface
    {
        return new FactFinderNgTrackClickAdapter(
            $this->client,
            $this->utilEncodingService,
            $this->config
        );
    }
}
