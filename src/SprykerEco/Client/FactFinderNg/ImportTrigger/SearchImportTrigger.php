<?php

namespace SprykerEco\Client\FactFinderNg\ImportTrigger;

use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface;
use SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface;
use SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface;

class SearchImportTrigger implements ImportTriggerInterface
{
    /**
     * @var FactFinderNgRequestMapperInterface
     */
    protected $requestMapper;

    /**
     * @var ResponseParserInterface
     */
    protected $responseParser;

    /**
     * @var AdapterFactoryInterface
     */
    protected $adapterFactory;

    /**
     * @param FactFinderNgRequestMapperInterface $requestMapper
     * @param ResponseParserInterface $responseParser
     * @param AdapterFactoryInterface $adapterFactory
     */
    public function __construct(
        FactFinderNgRequestMapperInterface $requestMapper,
        ResponseParserInterface $responseParser,
        AdapterFactoryInterface $adapterFactory
    ) {
        $this->requestMapper = $requestMapper;
        $this->responseParser = $responseParser;
        $this->adapterFactory = $adapterFactory;
    }

    /**
     * @return FactFinderNgResponseTransfer
     */
    public function trigger(): FactFinderNgResponseTransfer
    {
        $requestTransfer = $this->requestMapper->mapTriggerSearchImportRequest();
        $response = $this->adapterFactory->createFactFinderImportSearchAdapter()->sendRequest($requestTransfer);

        return $this->responseParser->parseResponse($response);
    }
}
