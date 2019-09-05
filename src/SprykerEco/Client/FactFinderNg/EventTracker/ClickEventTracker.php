<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\EventTracker;

use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface;
use SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface;
use SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface;

class ClickEventTracker implements EventTrackerInterface
{
    /**
     * @var \SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface
     */
    protected $requestMapper;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface
     */
    protected $adapterFactory;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface
     */
    protected $responseParser;

    /**
     * @param \SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface $requestMapper
     * @param \SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface $adapterFactory
     * @param \SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface $responseParser
     */
    public function __construct(
        FactFinderNgRequestMapperInterface $requestMapper,
        AdapterFactoryInterface $adapterFactory,
        ResponseParserInterface $responseParser
    ) {
        $this->requestMapper = $requestMapper;
        $this->adapterFactory = $adapterFactory;
        $this->responseParser = $responseParser;
    }

    /**
     * @param \Generated\Shared\Transfer\ClickEventTransfer[] $eventTransfers
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function track(array $eventTransfers): FactFinderNgResponseTransfer
    {
        $trackCheckoutRequestTransfer = $this->requestMapper
            ->mapTrackClickEventRequest($eventTransfers);

        $response = $this->adapterFactory
            ->createFactFinderNgTrackClickApiAdapter()
            ->sendRequest($trackCheckoutRequestTransfer);

        return $this->responseParser->parseResponse($response);
    }
}
