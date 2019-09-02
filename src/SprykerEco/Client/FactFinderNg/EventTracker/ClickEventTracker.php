<?php

namespace SprykerEco\Client\FactFinderNg\EventTracker;

use Generated\Shared\Transfer\ClickEventTransfer;
use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface;
use SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface;
use SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface;

class ClickEventTracker implements EventTrackerInterface
{
    /**
     * @var FactFinderNgRequestMapperInterface
     */
    protected $requestMapper;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface
     */
    protected $adapterFactory;

    /**
     * @var ResponseParserInterface
     */
    protected $responseParser;

    /**
     * @param FactFinderNgRequestMapperInterface $requestMapper
     * @param AdapterFactoryInterface $adapterFactory
     * @param ResponseParserInterface $responseParser
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
     * @param ClickEventTransfer[] $eventTransfers
     *
     * @return FactFinderNgResponseTransfer
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
