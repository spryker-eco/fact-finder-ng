<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Mapper\Elastica;

use Elastica\Query;
use Elastica\Response;
use Elastica\ResultSet;
use Elastica\ResultSet\DefaultBuilder;
use ErrorException;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Client\Search\Plugin\Elasticsearch\QueryExpander\CompletionQueryExpanderPlugin;
use Spryker\Client\Search\Plugin\Elasticsearch\QueryExpander\SuggestionByTypeQueryExpanderPlugin;
use SprykerEco\Client\FactFinderNg\Dependency\Client\FactFinderNgToPriceProductStorageClientInterface;
use SprykerEco\Client\FactFinderNg\Dependency\Client\FactFinderNgToProductImageStorageClientInterface;
use SprykerEco\Client\FactFinderNg\Dependency\Client\FactFinderNgToProductStorageClientInterface;

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
     * @var \SprykerEco\Client\FactFinderNg\Dependency\Client\FactFinderNgToProductImageStorageClientInterface
     */
    protected $productImageStorageClient;

    /**
     * @param \Elastica\ResultSet\DefaultBuilder $elasticaDefaultBuilder
     * @param \SprykerEco\Client\FactFinderNg\Dependency\Client\FactFinderNgToProductStorageClientInterface $productStorageClient
     * @param \SprykerEco\Client\FactFinderNg\Dependency\Client\FactFinderNgToProductImageStorageClientInterface $productImageStorageClient
     * @param \SprykerEco\Client\FactFinderNg\Dependency\Client\FactFinderNgToPriceProductStorageClientInterface $priceProductStorageClient
     */
    public function __construct(
        DefaultBuilder $elasticaDefaultBuilder,
        FactFinderNgToProductStorageClientInterface $productStorageClient,
        FactFinderNgToProductImageStorageClientInterface $productImageStorageClient,
        FactFinderNgToPriceProductStorageClientInterface $priceProductStorageClient
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
        $elasticaResponseArray[static::KEY_HITS] = [];
        $elasticaResponseArray[static::KEY_AGGREGATIONS] = $this->mapElasticaAggregations($searchResult);

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
        $ffSuggestCategoryItems = $this->findSuggestItemsByType($searchResult, static::KEY_CATEGORY);
        $ffSuggestBrandItems = $this->findSuggestItemsByType($searchResult, static::KEY_BRAND);
        $ffSuggestItems = array_merge($ffSuggestCategoryItems, $ffSuggestBrandItems);

        foreach ($ffSuggestItems as $ffSuggestItem) {
            $bucket = [
                static::KEY_KEY => $ffSuggestItem[static::KEY_NAME],
                static::KEY_DOC_COUNT => $ffSuggestItem[static::KEY_HIT_COUNT],
            ];

            $buckets[] = $bucket;
        }

        $completion = [
            static::KEY_DOC_COUNT_ERROR_UPPER_BOUND => 0,
            static::KEY_SUM_OTHER_DOC_COUNT => count($buckets),
            static::KEY_BUCKETS => $buckets,
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
            static::KEY_DOC_COUNT_ERROR_UPPER_BOUND => 0,
            static::KEY_SUM_OTHER_DOC_COUNT => 0,
            static::KEY_BUCKETS => $buckets,
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
        $ffSuggestItems = $this->findSuggestItemsByType($searchResult, static::KEY_PRODUCT_NAME);
        $hits = [];
        $maxScore = 0;

        foreach ($ffSuggestItems as $ffSuggestItem) {
            $productAbstract = $this->productStorageClient
                ->findProductAbstractStorageDataByMapping(
                    static::SKU_MAPPING_TYPE,
                    $ffSuggestItem[static::KEY_ATTRIBUTES][static::KEY_MASTER_ID],
                    $this->currentLocale
                );

            if ($productAbstract === null) {
                continue;
            }
            $maxScore = max($maxScore, $ffSuggestItem[static::KEY_SCORE]);

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
            if ($ffSuggestItem[static::KEY_OPTION_TYPE] == $ffSuggestType) {
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
            static::KEY_INDEX => $this->currentLocale . static::KEY_SEARCH,
            static::KEY_TYPE => static::KEY_PAGE,
            static::KEY_ID => $productAbstract[static::KEY_ID_PRODUCT_ABSTRACT],
            static::KEY_OPTION_SCORE => $ffSuggestItem[static::KEY_SCORE],
            static::KEY_SOURCE => [
                static::KEY_STORE => $this->currentStore->getName(),
                static::KEY_LOCALE => $this->currentLocale,
                static::KEY_OPTION_TYPE => static::KEY_PRODUCT_ABSTRACT,
                static::KEY_IS_ACTIVE => true,
                static::KEY_SEARCH_RESULT_DATA =>
                    [
                        static::KEY_ID_PRODUCT_ABSTRACT => $productAbstract[static::KEY_ID_PRODUCT_ABSTRACT],
                        static::KEY_ABSTRACT_SKU => $productAbstract[static::KEY_SKU],
                        static::KEY_ABSTRACT_NAME => $productAbstract[static::KEY_NAME],
                        static::KEY_URL => $productAbstract[static::KEY_URL],
                        static::KEY_OPTION_TYPE => static::KEY_PRODUCT_ABSTRACT,
                        static::KEY_PRICE => 0,
                        static::KEY_PRICES => $this->mapElasticaPrices($productAbstract),
                        static::KEY_IMAGES => $this->mapElasticaImages($this->productImageStorageClient
                            ->findProductImageAbstractStorageTransfer(
                                $productAbstract[static::KEY_ID_PRODUCT_ABSTRACT],
                                $this->currentLocale
                            )),
                        static::KEY_ID_PRODUCT_LABELS => [],
                    ],
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
            static::KEY_KEY => static::KEY_PRODUCT_ABSTRACT,
            static::KEY_DOC_COUNT => $hitsCount,
            static::KEY_TOP_HITS => [
                static::KEY_HITS => [
                    static::KEY_TOTAL => $hitsCount,
                    static::KEY_MAX_SCORE => $maxScore,
                    static::KEY_HITS => $hits,
                ],
            ],
        ];
    }
}
