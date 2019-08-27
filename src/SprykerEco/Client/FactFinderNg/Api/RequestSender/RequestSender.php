<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Api\RequestSender;

use Elastica\Query;
use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use Generated\Shared\Transfer\TrackCheckoutRequestTransfer;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface;
use SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface;
use SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface;

class RequestSender implements RequestSenderInterface
{
    /**
     * @var \SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface
     */
    protected $mapper;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface
     */
    protected $responseParser;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface
     */
    protected $adapterFactory;

    /**
     * @param \SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface $mapper
     * @param \SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface $responseParser
     * @param \SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface $adapterFactory
     */
    public function __construct(
        FactFinderNgRequestMapperInterface $mapper,
        ResponseParserInterface $responseParser,
        AdapterFactoryInterface $adapterFactory
    ) {
        $this->mapper = $mapper;
        $this->responseParser = $responseParser;
        $this->adapterFactory = $adapterFactory;
    }

    /**
     * @param \Elastica\Query $query
     * @param array $requestParameters
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function sendSearchRequest(Query $query, array $requestParameters): FactFinderNgResponseTransfer
    {
        $requestTransfer = $this->mapper->mapSearchRequest($requestParameters);
        $response = $this->adapterFactory
            ->createFactFinderNgSearchAdapter()
            ->sendRequest($requestTransfer);

        return $this->responseParser->parseResponse($response);
    }

    /**
     * @param \Elastica\Query $query
     * @param array $requestParameters
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function sendSuggestionRequest(Query $query, array $requestParameters): FactFinderNgResponseTransfer
    {
        $requestTransfer = $this->mapper->mapSuggestionRequest($requestParameters);
        $responseTransfer = $this->adapterFactory
            ->createFactFinderNgSuggestionAdapter()
            ->sendRequest($requestTransfer);

        return $this->responseParser->parseResponse($responseTransfer);
    }

    /**
     * @param TrackCheckoutRequestTransfer $trackCheckoutRequestTransfer
     *
     * @return FactFinderNgResponseTransfer
     */
    public function sendTrackCheckoutRequest(TrackCheckoutRequestTransfer $trackCheckoutRequestTransfer): FactFinderNgResponseTransfer
    {
        $response = $this->adapterFactory
            ->createFactFinderNgTrackCheckoutApiAdapter()
            ->sendRequest($trackCheckoutRequestTransfer);

        return $this->responseParser->parseResponse($response);
    }
}
