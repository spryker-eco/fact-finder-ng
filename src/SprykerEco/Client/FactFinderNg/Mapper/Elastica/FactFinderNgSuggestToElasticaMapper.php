<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Mapper\Elastica;

use Elastica\Query;
use Elastica\Response;
use Elastica\ResultSet;
use Elastica\ResultSet\DefaultBuilder;
use ErrorException;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Client\PriceProductStorage\PriceProductStorageClientInterface;
use Spryker\Client\ProductImageStorage\ProductImageStorageClientInterface;
use Spryker\Client\ProductStorage\ProductStorageClientInterface;
use Spryker\Client\Search\Plugin\Elasticsearch\QueryExpander\CompletionQueryExpanderPlugin;
use Spryker\Client\Search\Plugin\Elasticsearch\QueryExpander\SuggestionByTypeQueryExpanderPlugin;

class FactFinderNgSuggestToElasticaMapper extends AbstractFactFinderToElasticaMapper implements FactFinderToElasticaMapperInterface
{
    /**
     * @var \Elastica\ResultSet\DefaultBuilder
     */
    protected $elasticaDefaultBuilder;

    /**
     * @var \Spryker\Client\ProductStorage\ProductStorageClientInterface
     */
    protected $productStorageClient;

    /**
     * @var \Spryker\Client\ProductImageStorage\ProductImageStorageClientInterface
     */
    protected $productImageStorageClient;

    /**
     * @param \Elastica\ResultSet\DefaultBuilder $elasticaDefaultBuilder
     * @param \Spryker\Client\ProductStorage\ProductStorageClientInterface $productStorageClient
     * @param \Spryker\Client\ProductImageStorage\ProductImageStorageClientInterface $productImageStorageClient
     * @param \Spryker\Client\PriceProductStorage\PriceProductStorageClientInterface $priceProductStorageClient
     */
    public function __construct(
        DefaultBuilder $elasticaDefaultBuilder,
        ProductStorageClientInterface $productStorageClient,
        ProductImageStorageClientInterface $productImageStorageClient,
        PriceProductStorageClientInterface $priceProductStorageClient
    ) {
        parent::__construct($priceProductStorageClient);

        $this->elasticaDefaultBuilder = $elasticaDefaultBuilder;
        $this->productStorageClient = $productStorageClient;
        $this->productImageStorageClient = $productImageStorageClient;
    }

    /**
     * @param array $searchResult
     * @param \Elastica\Query $elasticaQuery
     * @param string $currentLocale
     * @param \Generated\Shared\Transfer\StoreTransfer $currentStore
     *
     * @return \Elastica\ResultSet
     */
    public function map(
        array $searchResult,
        Query $elasticaQuery,
        string $currentLocale,
        StoreTransfer $currentStore
    ): ResultSet {
        $this->currentLocale = $currentLocale;
        $this->currentStore = $currentStore;

        try {
            $elasticaResponseArray = $this->mapSearchResultToElasticaResponseArray($searchResult);
        } catch (ErrorException $e) {
            $elasticaResponseArray = [];
        }

        $elasticaResponse = new Response($elasticaResponseArray, 200);

        return $this->elasticaDefaultBuilder->buildResultSet($elasticaResponse, $elasticaQuery);
    }

    /**
     * @param array $searchResult
     *
     * @return array
     */
    protected function mapSearchResultToElasticaResponseArray(array $searchResult): array
    {
        $elasticaResponseArray = [];
        $elasticaResponseArray['hits'] = [];
        $elasticaResponseArray['aggregations'] = $this->mapElasticaAggregations($searchResult);

        return $elasticaResponseArray;
    }

    /**
     * @param array $searchResult
     *
     * @return array
     */
    protected function mapElasticaAggregations(array $searchResult): array
    {
        $aggregations = [];
        $aggregations[CompletionQueryExpanderPlugin::AGGREGATION_NAME] = $this->mapElasticaCompletion($searchResult);
        $aggregations[SuggestionByTypeQueryExpanderPlugin::AGGREGATION_NAME] = $this->mapElasticaSuggestion($searchResult);

        return $aggregations;
    }

    /**
     * @param array $searchResult
     *
     * @return array
     */
    protected function mapElasticaCompletion(array $searchResult): array
    {
        $buckets = [];
        $ffSuggestCategoryItems = $this->findSuggestItemsByType($searchResult, 'category');
        $ffSuggestBrandItems = $this->findSuggestItemsByType($searchResult, 'brand');
        $ffSuggestItems = array_merge($ffSuggestCategoryItems, $ffSuggestBrandItems);

        foreach ($ffSuggestItems as $ffSuggestItem) {
            $bucket = [
                'key' => $ffSuggestItem['name'],
                'doc_count' => $ffSuggestItem['hitCount'],
            ];

            $buckets[] = $bucket;
        }

        $completion = [
            'doc_count_error_upper_bound' => 0,
            'sum_other_doc_count' => count($buckets),
            'buckets' => $buckets,
        ];

        return $completion;
    }

    /**
     * @param array $searchResult
     *
     * @return array
     */
    protected function mapElasticaSuggestion(array $searchResult): array
    {
        $buckets = [];
        $bucket = $this->mapElasticaProductBucket($searchResult);

        if ($bucket) {
            $buckets[] = $bucket;
        }

        $suggestion = [
            'doc_count_error_upper_bound' => 0,
            'sum_other_doc_count' => 0,
            'buckets' => $buckets,
        ];

        return $suggestion;
    }

    /**
     * @param array $searchResult
     *
     * @return array
     */
    protected function mapElasticaProductBucket(array $searchResult): array
    {
        $ffSuggestItems = $this->findSuggestItemsByType($searchResult, 'productName');
        $hits = [];
        $maxScore = 0;

        foreach ($ffSuggestItems as $ffSuggestItem) {
            $productAbstract = $this->productStorageClient
                ->findProductAbstractStorageDataByMapping(
                    static::SKU_MAPPING_TYPE,
                    $ffSuggestItem['attributes']['masterId'],
                    $this->currentLocale
                );

            if ($productAbstract === null) {
                continue;
            }
            $maxScore = max($maxScore, $ffSuggestItem['score']);

            $hits[] = $this->mapHit($productAbstract, $ffSuggestItem);
        }

        $hitsCount = count($hits);

        if (!$hitsCount) {
            return [];
        }

        return $this->mapBucket($hitsCount, $maxScore, $hits);
    }

    /**
     * @param array $searchResult
     * @param string $ffSuggestType
     *
     * @return array
     */
    protected function findSuggestItemsByType(array $searchResult, string $ffSuggestType): array
    {
        $ffSuggestItems = [];
        foreach ($searchResult as $ffSuggestItem) {
            if ($ffSuggestItem['type'] == $ffSuggestType) {
                $ffSuggestItems[] = $ffSuggestItem;
            }
        }

        return $ffSuggestItems;
    }

    /**
     * @param array $productAbstract
     * @param array $ffSuggestItem
     *
     * @return array
     */
    protected function mapHit(array $productAbstract, array $ffSuggestItem): array
    {
        return [
            '_index' => $this->currentLocale . '_search',
            '_type' => 'page',
            '_id' => $productAbstract['id_product_abstract'],
            '_score' => $ffSuggestItem['score'],
            '_source' => [
                'store' => $this->currentStore->getName(),
                'locale' => $this->currentLocale,
                'type' => 'product_abstract',
                'is-active' => true,
                'search-result-data' =>
                    [
                        'id_product_abstract' => $productAbstract['id_product_abstract'],
                        'abstract_sku' => $productAbstract['sku'],
                        'abstract_name' => $productAbstract['name'],
                        'url' => $productAbstract['url'],
                        'type' => 'product_abstract',
                        'price' => 0,
                        'prices' => $this->mapElasticaPrices($productAbstract),
                        'images' => $this->mapElasticaImages($this->productImageStorageClient
                            ->findProductImageAbstractStorageTransfer(
                                $productAbstract['id_product_abstract'],
                                $this->currentLocale
                            )),
                        'id_product_labels' => [],
                    ],
                'full-text-boosted' => [],
                'full-text' => [],
                'suggestion-terms' => [],
                'completion-terms' => [],
                'string-sort' => [],
                'integer-sort' => [],
                'integer-facet' => [],
                'category' => [],
                'string-facet' => [],
            ],
        ];
    }

    /**
     * @param int $hitsCount
     * @param int $maxScore
     * @param array $hits
     *
     * @return array
     */
    protected function mapBucket(int $hitsCount, int $maxScore, array $hits): array
    {
        return [
            'key' => 'product_abstract',
            'doc_count' => $hitsCount,
            'top-hits' => [
                'hits' => [
                    'total' => $hitsCount,
                    'max_score' => $maxScore,
                    'hits' => $hits,
                ],
            ],
        ];
    }
}
