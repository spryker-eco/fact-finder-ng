<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Handler;

use Elastica\Query;
use Exception;
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
        } catch (Exception $exception) {
            $this->throwSearchException($exception, $query);
        }

        return $searchResult;
    }
}
