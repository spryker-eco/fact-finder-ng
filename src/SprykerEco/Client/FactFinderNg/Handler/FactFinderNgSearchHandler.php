<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Handler;

use Elastica\Query;
use Exception;
use Spryker\Client\Search\Exception\SearchResponseException;
use Spryker\Client\Search\Model\Handler\SearchHandlerInterface;

class FactFinderNgSearchHandler extends FactFinderHandler implements SearchHandlerInterface
{
    /**
     * @param \Elastica\Query $query
     * @param array $requestParameters
     *
     * @throws \Spryker\Client\Search\Exception\SearchResponseException
     *
     * @return array
     */
    protected function executeQuery(Query $query, array $requestParameters): array
    {
        try {
            $factFinderNgResponseTransfer = $this->requestSender->sendSearchRequest($query, $requestParameters);
            $searchResult = $factFinderNgResponseTransfer->getBody();
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
