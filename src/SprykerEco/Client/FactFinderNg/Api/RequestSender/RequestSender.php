<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Api\RequestSender;

use Elastica\Query;
use Generated\Shared\Transfer\FactFinderNgSearchResponseTransfer;
use Generated\Shared\Transfer\FactFinderNgSuggestionResponseTransfer;
use Generated\Shared\Transfer\FactFinderNgTrackCheckoutResponseTransfer;
use Generated\Shared\Transfer\TrackCheckoutRequestTransfer;
use Psr\Http\Message\ResponseInterface;
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
     * @return \Generated\Shared\Transfer\FactFinderNgSearchResponseTransfer
     */
    public function sendSearchRequest(Query $query, array $requestParameters): FactFinderNgSearchResponseTransfer
    {
        $requestTransfer = $this->mapper->mapSearchRequest($requestParameters);
        $responseTransfer = $this->adapterFactory
            ->createFactFinderNgSearchAdapter()
            ->sendRequest($requestTransfer);

        return $this->responseParser->parseSearchResponse($responseTransfer);
    }

    /**
     * @param \Elastica\Query $query
     * @param array $requestParameters
     *
     * @return \Generated\Shared\Transfer\FactFinderNgSuggestionResponseTransfer
     */
    public function sendSuggestionRequest(Query $query, array $requestParameters): FactFinderNgSuggestionResponseTransfer
    {
        $requestTransfer = $this->mapper->mapSuggestionRequest($requestParameters);
        $responseTransfer = $this->adapterFactory
            ->createFactFinderNgSuggestionAdapter()
            ->sendRequest($requestTransfer);

        return $this->responseParser->parseSuggestionResponse($responseTransfer);
    }

    /**
     * @param TrackCheckoutRequestTransfer $trackCheckoutRequestTransfer
     *
     * @return FactFinderNgTrackCheckoutResponseTransfer
     */
    public function sendTrackCheckoutRequest(TrackCheckoutRequestTransfer $trackCheckoutRequestTransfer): ResponseInterface
    {
        $responseTransfer = $this->adapterFactory
            ->createFactFinderNgTrackCheckoutApiAdapter()
            ->sendRequest($trackCheckoutRequestTransfer);

        return $this->responseParser->parseTrackCheckoutResponse($responseTransfer);
    }
}
