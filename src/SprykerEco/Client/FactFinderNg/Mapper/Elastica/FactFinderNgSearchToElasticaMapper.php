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

class FactFinderNgSearchToElasticaMapper extends AbstractFactFinderToElasticaMapper implements FactFinderToElasticaMapperInterface
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
        $elasticaResponseArray[static::KEY_HITS] = $this->mapElasticaHits($searchResult);
        $elasticaResponseArray[static::KEY_SORT_ITEMS] = $this->mapSortItems($searchResult);
//        $elasticaResponseArray[self::KEY_AGGREGATIONS] = json_decode('{"integer-facet":{"doc_count":290,"integer-facet-name":{"doc_count_error_upper_bound":0,"sum_other_doc_count":0,"buckets":[{"key":"price","doc_count":48,"integer-facet-stats":{"count":48,"min":3000,"max":345699,"avg":31361.4375,"sum":1505349}},{"key":"price-DEFAULT-CHF-GROSS_MODE","doc_count":48,"integer-facet-stats":{"count":48,"min":3450,"max":397554,"avg":36065.6875,"sum":1731153}},{"key":"price-DEFAULT-CHF-NET_MODE","doc_count":48,"integer-facet-stats":{"count":48,"min":3105,"max":357798,"avg":32459.25,"sum":1558044}},{"key":"price-DEFAULT-EUR-GROSS_MODE","doc_count":48,"integer-facet-stats":{"count":48,"min":3000,"max":345699,"avg":31361.4375,"sum":1505349}},{"key":"price-DEFAULT-EUR-NET_MODE","doc_count":48,"integer-facet-stats":{"count":48,"min":2700,"max":311129,"avg":28225.395833333332,"sum":1354819}},{"key":"price-ORIGINAL-CHF-GROSS_MODE","doc_count":12,"integer-facet-stats":{"count":12,"min":5750,"max":52325,"avg":25137,"sum":301644}},{"key":"price-ORIGINAL-CHF-NET_MODE","doc_count":12,"integer-facet-stats":{"count":12,"min":5175,"max":47093,"avg":22623.333333333332,"sum":271480}},{"key":"price-ORIGINAL-EUR-GROSS_MODE","doc_count":12,"integer-facet-stats":{"count":12,"min":5000,"max":45500,"avg":21858.25,"sum":262299}},{"key":"price-ORIGINAL-EUR-NET_MODE","doc_count":12,"integer-facet-stats":{"count":12,"min":4500,"max":40950,"avg":19672.416666666668,"sum":236069}},{"key":"rating","doc_count":2,"integer-facet-stats":{"count":2,"min":400,"max":425,"avg":412.5,"sum":825}}]}},"category.all-parents.category":{"doc_count_error_upper_bound":0,"sum_other_doc_count":0,"buckets":[{"key":1,"doc_count":48},{"key":2,"doc_count":26},{"key":4,"doc_count":14},{"key":3,"doc_count":12},{"key":9,"doc_count":10},{"key":10,"doc_count":10},{"key":5,"doc_count":6},{"key":8,"doc_count":5},{"key":11,"doc_count":5},{"key":12,"doc_count":5},{"key":14,"doc_count":2},{"key":6,"doc_count":1},{"key":13,"doc_count":1}]},"string-facet":{"doc_count":125,"string-facet-name":{"doc_count_error_upper_bound":0,"sum_other_doc_count":0,"buckets":[{"key":"brand","doc_count":48,"string-facet-value":{"doc_count_error_upper_bound":0,"sum_other_doc_count":0,"buckets":[{"key":"Sony","doc_count":43},{"key":"Asus","doc_count":4},{"key":"DELL","doc_count":1}]}},{"key":"color","doc_count":44,"string-facet-value":{"doc_count_error_upper_bound":0,"sum_other_doc_count":1,"buckets":[{"key":"Black","doc_count":22},{"key":"White","doc_count":8},{"key":"Silver","doc_count":4},{"key":"Grey","doc_count":2},{"key":"Pink","doc_count":2},{"key":"Copper","doc_count":1},{"key":"Gold","doc_count":1},{"key":"Orange","doc_count":1},{"key":"Purple","doc_count":1},{"key":"Red","doc_count":1}]}},{"key":"label","doc_count":25,"string-facet-value":{"doc_count_error_upper_bound":0,"sum_other_doc_count":0,"buckets":[{"key":"5","doc_count":12},{"key":"3","doc_count":11},{"key":"1","doc_count":1},{"key":"2","doc_count":1}]}},{"key":"weight","doc_count":8,"string-facet-value":{"doc_count_error_upper_bound":0,"sum_other_doc_count":0,"buckets":[{"key":"45 g","doc_count":4},{"key":"58 g","doc_count":2},{"key":"63.5 g","doc_count":2}]}}]}}}', true);

        return $elasticaResponseArray;
    }

    /**
     * @param array $searchResult
     *
     * @return array
     */
    protected function mapElasticaHits(array $searchResult): array
    {

        $total = $searchResult[static::KEY_TOTAL_HITS];
        $maxScore = max($searchResult[static::KEY_SCORE_FIRST_HIT], $searchResult[static::KEY_SCORE_LAST_HIT]);
        $elasticaHits = [];
        foreach ($searchResult[static::KEY_HITS] as $searchHit) {
            if (!count($searchHit[static::KEY_VARIANT_VALUES])) {
                continue;
            }

            $productAbstract = $this->productStorageClient
                ->findProductAbstractStorageDataByMapping(
                    static::SKU_MAPPING_TYPE,
                    $searchHit[static::KEY_OPTION_ID],
                    $this->currentLocale
                );
            if ($productAbstract === null) {
                continue;
            }
            $productAbstractImageStorageTransfer = $this->productImageStorageClient
                ->findProductImageAbstractStorageTransfer(
                    $productAbstract[static::KEY_ID_PRODUCT_ABSTRACT],
                    $this->currentLocale
                );

            $elasticaImages = $this->mapElasticaImages($productAbstractImageStorageTransfer);
            $elasticaPrices = $this->mapElasticaPrices($productAbstract);

            $elasticaHit = [
                static::KEY_INDEX => $this->currentLocale . static::KEY_SEARCH,
                static::KEY_TYPE => static::KEY_PAGE,
                static::KEY_ID => $productAbstract[static::KEY_ID_PRODUCT_ABSTRACT],
                static::KEY_SCORE => $searchHit[static::KEY_SCORE],
                static::KEY_SOURCE =>
                    [
                        static::KEY_SEARCH_RESULT_DATA =>
                            [
                                static::KEY_IMAGES => $elasticaImages,
                                static::KEY_ID_PRODUCT_LABELS => [],
                                static::KEY_PRICE => 0,
                                static::KEY_ABSTRACT_NAME => $productAbstract[static::KEY_NAME],
                                static::KEY_ID_PRODUCT_ABSTRACT => $productAbstract[static::KEY_ID_PRODUCT_ABSTRACT],
                                static::KEY_OPTION_TYPE => static::KEY_PRODUCT_ABSTRACT,
                                static::KEY_PRICES => $elasticaPrices,
                                static::KEY_ABSTRACT_SKU => $productAbstract[static::KEY_SKU],
                                static::KEY_URL => $productAbstract[static::KEY_URL],
                            ],
                    ],
            ];

            $elasticaHits[] = $elasticaHit;
        }

        return [
            static::KEY_TOTAL => $total,
            static::KEY_MAX_SCORE => $maxScore,
            static::KEY_HITS => $elasticaHits,
        ];
    }

    /**
     * @param array $searchResult
     *
     * @return array
     */
    protected function mapSortItems(array $searchResult): array
    {
        return $searchResult[static::KEY_SORT_ITEMS] ?? [];
    }
}
