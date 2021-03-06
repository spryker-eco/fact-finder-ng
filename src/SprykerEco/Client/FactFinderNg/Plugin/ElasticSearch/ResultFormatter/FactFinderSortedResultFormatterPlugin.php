<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Plugin\ElasticSearch\ResultFormatter;

use Elastica\ResultSet;
use Generated\Shared\Transfer\SortSearchResultTransfer;
use Spryker\Client\Search\Plugin\Elasticsearch\ResultFormatter\AbstractElasticsearchResultFormatterPlugin;

/**
 * @method \SprykerEco\Client\FactFinderNg\FactFinderNgFactory getFactory()
 */
class FactFinderSortedResultFormatterPlugin extends AbstractElasticsearchResultFormatterPlugin
{
    public const NAME = 'sort';
    public const SORT_ITEMS = 'sortItems';
    public const KEY_NAME = 'name';
    public const KEY_ORDER = 'order';

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @param \Elastica\ResultSet $searchResult
     * @param array $requestParameters
     *
     * @return mixed
     */
    protected function formatSearchResult(ResultSet $searchResult, array $requestParameters)
    {
        $sortSearchResultTransfer = new SortSearchResultTransfer();
        $sortSearchResultTransfer
            ->setSortParamNames($this->mapSortParamNames($searchResult->getResponse()->getData()[static::SORT_ITEMS] ?? []))
            ->setCurrentSortParam($this->getCurrentSortParam($requestParameters));

        return $sortSearchResultTransfer;
    }

    /**
     * @param array $sortItems
     *
     * @return array
     */
    protected function mapSortParamNames(array $sortItems): array
    {
        $paramNames = [];

        foreach ($sortItems as $item) {
            $paramNames[] = sprintf('%s_%s', mb_strtolower($item[static::KEY_NAME]), $item[static::KEY_ORDER]);
        }

        return $paramNames;
    }

    /**
     * @param array $requestParameters
     *
     * @return string
     */
    protected function getCurrentSortParam(array $requestParameters): string
    {
        return $requestParameters[static::NAME] ?? '';
    }
}
