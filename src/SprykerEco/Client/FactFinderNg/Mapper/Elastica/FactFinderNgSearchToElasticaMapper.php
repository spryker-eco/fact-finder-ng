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
        $elasticaResponseArray['hits'] = $this->mapElasticaHits($searchResult);
        $elasticaResponseArray['aggregations'] = [];

        return $elasticaResponseArray;
    }

    /**
     * @param array $searchResult
     *
     * @return array
     */
    protected function mapElasticaHits(array $searchResult): array
    {

        $total = $searchResult['totalHits'];
        $maxScore = max($searchResult['scoreFirstHit'], $searchResult['scoreLastHit']);
        $elasticaHits = [];
        foreach ($searchResult['hits'] as $searchHit) {
            if (!count($searchHit['variantValues'])) {
                continue;
            }

            $productAbstract = $this->productStorageClient
                ->findProductAbstractStorageDataByMapping(
                    static::SKU_MAPPING_TYPE,
                    $searchHit['id'],
                    $this->currentLocale
                );
            if ($productAbstract === null) {
                continue;
            }
            $productAbstractImageStorageTransfer = $this->productImageStorageClient
                ->findProductImageAbstractStorageTransfer(
                    $productAbstract['id_product_abstract'],
                    $this->currentLocale
                );

            $elasticaImages = $this->mapElasticaImages($productAbstractImageStorageTransfer);
            $elasticaPrices = $this->mapElasticaPrices($productAbstract);

            $elasticaHit = [
                '_index' => $this->currentLocale . '_search',
                '_type' => 'page',
                '_id' => $productAbstract['id_product_abstract'],
                '_score' => $searchHit['score'],
                '_source' =>
                    [
                        'search-result-data' =>
                            [
                                'images' => $elasticaImages,
                                'id_product_labels' => [],
                                'price' => 0,
                                'abstract_name' => $productAbstract['name'],
                                'id_product_abstract' => $productAbstract['id_product_abstract'],
                                'type' => 'product_abstract',
                                'prices' => $elasticaPrices,
                                'abstract_sku' => $productAbstract['sku'],
                                'url' => $productAbstract['url'],
                            ],
                    ],
            ];

            $elasticaHits[] = $elasticaHit;
        }

        return [
            'total' => $total,
            'max_score' => $maxScore,
            'hits' => $elasticaHits,
        ];
    }
}
