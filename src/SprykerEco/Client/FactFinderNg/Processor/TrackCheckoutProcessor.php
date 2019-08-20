<?php

namespace SprykerEco\Client\FactFinderNg\Processor;

use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface;
use SprykerEco\Client\FactFinderNg\Mapper\Request\Track\TrackApiRequestMapperInterface;
use SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface;

class TrackCheckoutProcessor implements FactFinderNgRequestProcessorInterface
{
    /**
     * @var TrackApiRequestMapperInterface
     */
    protected $trackApiRequestMapper;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface
     */
    protected $adapterFactory;

    /**
     * @var ResponseParserInterface
     */
    protected $responseParser;


    /**
     * @param TrackApiRequestMapperInterface $trackApiRequestMapper
     * @param AdapterFactoryInterface $adapterFactory
     * @param ResponseParserInterface $responseParser
     */
    public function __construct(
        TrackApiRequestMapperInterface $trackApiRequestMapper,
        AdapterFactoryInterface $adapterFactory,
        ResponseParserInterface $responseParser
    ) {
        $this->trackApiRequestMapper = $trackApiRequestMapper;
        $this->adapterFactory = $adapterFactory;
        $this->responseParser = $responseParser;
    }

    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return FactFinderNgResponseTransfer
     */
    public function processApiRequest(QuoteTransfer $quoteTransfer): FactFinderNgResponseTransfer
    {
        $trackCheckoutRequestTransfer = $this->trackApiRequestMapper
            ->mapQuoteTransferToTrackCheckoutRequestTransfer($quoteTransfer);

        $response = $this->adapterFactory
            ->createFactFinderNgTrackCheckoutApiAdapter()
            ->sendRequest($trackCheckoutRequestTransfer);

        return $this->responseParser->parseResponse($response);
    }
}
