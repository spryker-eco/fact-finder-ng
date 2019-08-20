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
    public const ELASTICA_RESPONSE_KEY_TOTAL = 'total';
    public const ELASTICA_RESPONSE_KEY_MAX_SCORE = 'max_score';
    public const ELASTICA_RESPONSE_KEY_HITS = 'hits';
    public const ELASTICA_RESPONSE_KEY_INDEX = '_index';
    public const ELASTICA_RESPONSE_KEY_SEARCH = '_search';
    public const ELASTICA_RESPONSE_KEY_TYPE = '_type';
    public const ELASTICA_RESPONSE_KEY_PAGE = 'page';
    public const ELASTICA_RESPONSE_KEY_ID = '_id';
    public const ELASTICA_RESPONSE_KEY_SCORE = '_score';
    public const ELASTICA_RESPONSE_KEY_SOURCE = '_source';
    public const ELASTICA_RESPONSE_KEY_SEARCH_RESULT_DATA = 'search-result-data';
    public const ELASTICA_RESPONSE_KEY_IMAGES = 'images';
    public const ELASTICA_RESPONSE_KEY_ID_PRODUCT_LABELS = 'id_product_labels';
    public const ELASTICA_RESPONSE_KEY_PRICE = 'price';
    public const ELASTICA_RESPONSE_KEY_ABSTRACT_NAME = 'abstract_name';
    public const ELASTICA_RESPONSE_KEY_ID_PRODUCT_ABSTRACT = 'id_product_abstract';
    public const ELASTICA_RESPONSE_KEY_OPTION_TYPE = 'type';
    public const ELASTICA_RESPONSE_KEY_PRODUCT_ABSTRACT = 'product_abstract';
    public const ELASTICA_RESPONSE_KEY_PRICES = 'prices';
    public const ELASTICA_RESPONSE_KEY_ABSTRACT_SKU = 'abstract_sku';
    public const ELASTICA_RESPONSE_KEY_URL = 'url';
    public const ELASTICA_RESPONSE_KEY_AGGREGATIONS = 'aggregations';

    public const FACT_FINDER_RESPONSE_VARIANT_VALUES = 'variantValues';
    public const FACT_FINDER_RESPONSE_TOTAL_HITS = 'totalHits';
    public const FACT_FINDER_RESPONSE_SCORE_FIRST_HIT = 'scoreFirstHit';
    public const FACT_FINDER_RESPONSE_SCORE_LAST_HIT = 'scoreLastHit';
    public const FACT_FINDER_RESPONSE_KEY_HITS = 'hits';
    public const FACT_FINDER_RESPONSE_KEY_ID = 'id';
    public const FACT_FINDER_RESPONSE_KEY_SCORE = 'score';

    public const ABSTRACT_PRODUCT_KEY_ID = 'id_product_abstract';
    public const ABSTRACT_PRODUCT_KEY_NAME = 'name';
    public const ABSTRACT_PRODUCT_KEY_SKU = 'sku';
    public const ABSTRACT_PRODUCT_KEY_URL = 'url';


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
        $elasticaResponseArray[static::ELASTICA_RESPONSE_KEY_HITS] = $this->mapElasticaHits($searchResult);
        $elasticaResponseArray[self::ELASTICA_RESPONSE_KEY_AGGREGATIONS] = [];

        return $elasticaResponseArray;
    }

    /**
     * @param array $searchResult
     *
     * @return array
     */
    protected function mapElasticaHits(array $searchResult): array
    {

        $total = $searchResult[static::FACT_FINDER_RESPONSE_TOTAL_HITS];
        $maxScore = max($searchResult[static::FACT_FINDER_RESPONSE_SCORE_FIRST_HIT], $searchResult[static::FACT_FINDER_RESPONSE_SCORE_LAST_HIT]);
        $elasticaHits = [];
        foreach ($searchResult[static::FACT_FINDER_RESPONSE_KEY_HITS] as $searchHit) {
            if (!count($searchHit[static::FACT_FINDER_RESPONSE_VARIANT_VALUES])) {
                continue;
            }

            $productAbstract = $this->productStorageClient
                ->findProductAbstractStorageDataByMapping(
                    static::SKU_MAPPING_TYPE,
                    $searchHit[static::FACT_FINDER_RESPONSE_KEY_ID],
                    $this->currentLocale
                );
            if ($productAbstract === null) {
                continue;
            }
            $productAbstractImageStorageTransfer = $this->productImageStorageClient
                ->findProductImageAbstractStorageTransfer(
                    $productAbstract[static::ABSTRACT_PRODUCT_KEY_ID],
                    $this->currentLocale
                );

            $elasticaImages = $this->mapElasticaImages($productAbstractImageStorageTransfer);
            $elasticaPrices = $this->mapElasticaPrices($productAbstract);

            $elasticaHit = [
                static::ELASTICA_RESPONSE_KEY_INDEX => $this->currentLocale . static::ELASTICA_RESPONSE_KEY_SEARCH,
                static::ELASTICA_RESPONSE_KEY_TYPE => static::ELASTICA_RESPONSE_KEY_PAGE,
                static::ELASTICA_RESPONSE_KEY_ID => $productAbstract[static::ABSTRACT_PRODUCT_KEY_ID],
                static::ELASTICA_RESPONSE_KEY_SCORE => $searchHit[static::FACT_FINDER_RESPONSE_KEY_SCORE],
                static::ELASTICA_RESPONSE_KEY_SOURCE =>
                    [
                        static::ELASTICA_RESPONSE_KEY_SEARCH_RESULT_DATA =>
                            [
                                static::ELASTICA_RESPONSE_KEY_IMAGES => $elasticaImages,
                                static::ELASTICA_RESPONSE_KEY_ID_PRODUCT_LABELS => [],
                                static::ELASTICA_RESPONSE_KEY_PRICE => 0,
                                static::ELASTICA_RESPONSE_KEY_ABSTRACT_NAME => $productAbstract[static::ABSTRACT_PRODUCT_KEY_NAME],
                                static::ELASTICA_RESPONSE_KEY_ID_PRODUCT_ABSTRACT => $productAbstract[static::ABSTRACT_PRODUCT_KEY_ID],
                                static::ELASTICA_RESPONSE_KEY_OPTION_TYPE => static::ELASTICA_RESPONSE_KEY_PRODUCT_ABSTRACT,
                                static::ELASTICA_RESPONSE_KEY_PRICES => $elasticaPrices,
                                static::ELASTICA_RESPONSE_KEY_ABSTRACT_SKU => $productAbstract[static::ABSTRACT_PRODUCT_KEY_SKU],
                                static::ELASTICA_RESPONSE_KEY_URL => $productAbstract[static::ABSTRACT_PRODUCT_KEY_URL],
                            ],
                    ],
            ];

            $elasticaHits[] = $elasticaHit;
        }

        return [
            static::ELASTICA_RESPONSE_KEY_TOTAL => $total,
            static::ELASTICA_RESPONSE_KEY_MAX_SCORE => $maxScore,
            static::ELASTICA_RESPONSE_KEY_HITS => $elasticaHits,
        ];
    }
}
