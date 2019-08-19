<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Plugin;

use Pyz\Client\Search\Plugin\SearchHandlerPluginInterface;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

/**
 * @method \SprykerEco\Client\FactFinderNg\FactFinderNgClientInterface getClient()
 */
class FactFinderNgSearchHandlerPlugin extends AbstractPlugin implements SearchHandlerPluginInterface
{
    /**
     * @param \Spryker\Client\Search\Dependency\Plugin\QueryInterface $searchQuery
     * @param array $resultFormatters
     * @param array $requestParameters
     *
     * @return array|\Elastica\ResultSet
     */
    public function handle(QueryInterface $searchQuery, array $resultFormatters = [], array $requestParameters = [])
    {
        return $this->getClient()->search($searchQuery, $resultFormatters, $requestParameters);
    }

    /**
     * @param array $requestParameters
     *
     * @return bool
     */
    public function isApplicable(array $requestParameters): bool
    {
        if (isset($requestParameters['suggest']) && $requestParameters['suggest']) {
            return false;
        }

        if (!$requestParameters) {
            return false;
        }

        return true;
    }
}
