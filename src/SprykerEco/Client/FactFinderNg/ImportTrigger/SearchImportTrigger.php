<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\ImportTrigger;

use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface;
use SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface;
use SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface;

class SearchImportTrigger implements ImportTriggerInterface
{
    /**
     * @var \SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface
     */
    protected $requestMapper;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface
     */
    protected $responseParser;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface
     */
    protected $adapterFactory;

    /**
     * @param \SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface $requestMapper
     * @param \SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface $responseParser
     * @param \SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface $adapterFactory
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
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function trigger(): FactFinderNgResponseTransfer
    {
        $requestTransfer = $this->requestMapper->mapTriggerSearchImportRequest();
        $response = $this->adapterFactory->createFactFinderImportSearchAdapter()->sendRequest($requestTransfer);

        return $this->responseParser->parseResponse($response);
    }
}
