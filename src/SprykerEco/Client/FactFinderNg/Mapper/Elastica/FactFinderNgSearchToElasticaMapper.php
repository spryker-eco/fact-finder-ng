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
        $elasticaResponseArray[self::KEY_AGGREGATIONS] = [];

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
}
