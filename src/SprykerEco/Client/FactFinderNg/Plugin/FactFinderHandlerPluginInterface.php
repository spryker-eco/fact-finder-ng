<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Plugin;

use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

interface FactFinderHandlerPluginInterface
{
    protected const CATEGORY = 'category';

    /**
     * Specification:
     * - Method handles search request by provided query.
     * - Result formatters and request parameters might be provided.
     *
     * @api
     *
     * @param \Spryker\Client\Search\Dependency\Plugin\QueryInterface $searchQuery
     * @param array $resultFormatters
     * @param array $requestParameters
     *
     * @return array|\Elastica\ResultSet
     */
    public function handle(QueryInterface $searchQuery, array $resultFormatters = [], array $requestParameters = []);

    /**
     * Specification:
     * - Check if plugin should be used.
     *
     * @api
     *
     * @param array $requestParameters
     *
     * @return bool
     */
    public function isApplicable(array $requestParameters): bool;
}
