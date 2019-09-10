<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Plugin;

use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

/**
 * @method \SprykerEco\Client\FactFinderNg\FactFinderNgClientInterface getClient()
 */
class FactFinderNgSuggestHandlerPlugin extends AbstractPlugin implements FactFinderHandlerPluginInterface
{
    protected const PARAM_SUGGEST = 'suggest';

    /**
     * {@inheritdoc}
     * - The method uses suggest API request for handling search query.
     *
     * @api
     *
     * @param \Spryker\Client\Search\Dependency\Plugin\QueryInterface $searchQuery
     * @param array $resultFormatters
     * @param array $requestParameters
     *
     * @return mixed
     */
    public function handle(QueryInterface $searchQuery, array $resultFormatters = [], array $requestParameters = [])
    {
        return $this->getClient()->suggest($searchQuery, $resultFormatters, $requestParameters);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param array $requestParameters
     *
     * @return bool
     */
    public function isApplicable(array $requestParameters): bool
    {
        return isset($requestParameters[static::PARAM_SUGGEST]) && $requestParameters[static::PARAM_SUGGEST];
    }
}
