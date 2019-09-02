<?php

namespace SprykerEco\Client\FactFinderNg\Handler;

use Elastica\Query;
use Exception;
use Spryker\Client\Search\Exception\SearchResponseException;
use Spryker\Client\Search\Model\Handler\SearchHandlerInterface;

class FactFinderNavigationHandler extends FactFinderHandler implements SearchHandlerInterface
{
    /**
     * @param \Elastica\Query $query
     * @param array $requestParameters
     *
     * @return array
     */
    protected function executeQuery(Query $query, array $requestParameters): array
    {
        try {
            $requestTransfer = $this->requestMapper->mapNavigationRequest($requestParameters);
            $response = $this->adapterFactory->createFactFinderNgNavigationAdapter()->sendRequest($requestTransfer);
            $responseTransfer = $this->responseParser->parseResponse($response);
            $searchResult = $responseTransfer->getBody();
        } catch (Exception $e) {
            throw new SearchResponseException(
                sprintf("Search failed with the following reason: %s. Query: %s", $e->getMessage()),
                $e->getCode(),
                $e
            );
        }

        return $searchResult;
    }
}
