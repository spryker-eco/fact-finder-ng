<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Mapper\Elastica;

use Generated\Shared\Transfer\ProductAbstractImageStorageTransfer;
use Spryker\Client\PriceProductStorage\PriceProductStorageClientInterface;

abstract class AbstractFactFinderToElasticaMapper
{
    public const SKU_MAPPING_TYPE = 'sku';
    public const GROSS_MODE = 'GROSS_MODE';
    public const NET_MODE = 'NET_MODE';

    public const KEY_TOTAL = 'total';
    public const KEY_MAX_SCORE = 'max_score';
    public const KEY_HITS = 'hits';
    public const KEY_TOP_HITS = 'top-hits';
    public const KEY_INDEX = '_index';
    public const KEY_SEARCH = '_search';
    public const KEY_TYPE = '_type';
    public const KEY_PAGE = 'page';
    public const KEY_ID = '_id';
    public const KEY_OPTION_ID = 'id';
    public const KEY_OPTION_SCORE = '_score';
    public const KEY_SCORE = 'score';
    public const KEY_SOURCE = '_source';
    public const KEY_SEARCH_RESULT_DATA = 'search-result-data';
    public const KEY_IMAGES = 'images';
    public const KEY_ID_PRODUCT_LABELS = 'id_product_labels';
    public const KEY_PRICE = 'price';
    public const KEY_ABSTRACT_NAME = 'abstract_name';
    public const KEY_ID_PRODUCT_ABSTRACT = 'id_product_abstract';
    public const KEY_OPTION_TYPE = 'type';
    public const KEY_PRODUCT_ABSTRACT = 'product_abstract';
    public const KEY_PRICES = 'prices';
    public const KEY_ABSTRACT_SKU = 'abstract_sku';
    public const KEY_URL = 'url';
    public const KEY_AGGREGATIONS = 'aggregations';
    public const KEY_KEY = 'key';
    public const KEY_DOC_COUNT = 'doc_count';
    public const KEY_VARIANT_VALUES = 'variantValues';
    public const KEY_TOTAL_HITS = 'totalHits';
    public const KEY_SCORE_FIRST_HIT = 'scoreFirstHit';
    public const KEY_SCORE_LAST_HIT = 'scoreLastHit';
    public const KEY_NAME = 'name';
    public const KEY_SKU = 'sku';
    public const KEY_FK_PRODUCT_IMAGE_SET = 'fk_product_image_set';
    public const KEY_FK_PRODUCT_IMAGE = 'fk_product_image';
    public const KEY_ID_PRODUCT_IMAGE = 'id_product_image';
    public const KEY_SORT_ORDER = 'sort_order';
    public const KEY_SORT_ITEMS = 'sortItems';
    public const KEY_ID_PRODUCT_IMAGE_SET_TO_PRODUCT_IMAGE = 'id_product_image_set_to_product_image';
    public const KEY_CREATED_AT = 'created_at';
    public const KEY_EXTERNAL_URL_LARGE = 'external_url_large';
    public const KEY_EXTERNAL_URL_SMALL = 'external_url_small';
    public const KEY_UPDATED_AT = 'updated_at';
    public const KEY_HIT_COUNT = 'hitCount';
    public const KEY_BUCKETS = 'buckets';
    public const KEY_SUM_OTHER_DOC_COUNT = 'sum_other_doc_count';
    public const KEY_DOC_COUNT_ERROR_UPPER_BOUND = 'doc_count_error_upper_bound';
    public const KEY_CATEGORY = 'category';
    public const KEY_BRAND = 'brand';
    public const KEY_PRODUCT_NAME = 'productName';
    public const KEY_ATTRIBUTES = 'attributes';
    public const KEY_MASTER_ID = 'masterId';
    public const KEY_STORE = 'store';
    public const KEY_LOCALE = 'locale';
    public const KEY_IS_ACTIVE = 'is-active';

    /**
     * @var \Spryker\Client\PriceProductStorage\PriceProductStorageClientInterface
     */
    protected $priceProductStorageClient;

    /**
     * @var string
     */
    protected $currentLocale;

    /**
     * @var \Generated\Shared\Transfer\StoreTransfer
     */
    protected $currentStore;

    /**
     * @param \Spryker\Client\PriceProductStorage\PriceProductStorageClientInterface $priceProductStorageClient
     */
    public function __construct(
        PriceProductStorageClientInterface $priceProductStorageClient
    ) {
        $this->priceProductStorageClient = $priceProductStorageClient;
    }

    /**
     * @param array $productAbstract
     *
     * @return array
     */
    protected function mapElasticaPrices(array $productAbstract): array
    {
        $elasticaPrices = [];
        $priceProductAbstractTransfers = $this->priceProductStorageClient
            ->getPriceProductAbstractTransfers($productAbstract['id_product_abstract']);

        foreach ($priceProductAbstractTransfers as $priceProductAbstractTransfer) {
            $currencyCode = $priceProductAbstractTransfer->getMoneyValue()
                ->getCurrency()
                ->getCode();
            if (!isset($elasticaPrices[$currencyCode])) {
                $elasticaPrices[$currencyCode] = [
                    static::GROSS_MODE => [],
                    static::NET_MODE => [],
                ];
            }

            $elasticaPrices[$currencyCode][static::GROSS_MODE][$priceProductAbstractTransfer->getPriceTypeName()] =
                $priceProductAbstractTransfer->getMoneyValue()->getGrossAmount();

            $elasticaPrices[$currencyCode][static::NET_MODE][$priceProductAbstractTransfer->getPriceTypeName()] =
                $priceProductAbstractTransfer->getMoneyValue()->getNetAmount();
        }

        return $elasticaPrices;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractImageStorageTransfer $productAbstractImageStorageTransfer
     *
     * @return array
     */
    protected function mapElasticaImages(ProductAbstractImageStorageTransfer $productAbstractImageStorageTransfer): array
    {
        $elasticaImages = [];
        if ($productAbstractImageStorageTransfer !== null) {
            foreach ($productAbstractImageStorageTransfer->getImageSets() as $productImageSetStorageTransfer) {
                foreach ($productImageSetStorageTransfer->getImages() as $productImageStorageTransfer) {
                    $elasticaImages[] = [
                        static::KEY_FK_PRODUCT_IMAGE_SET => '',
                        static::KEY_ID_PRODUCT_IMAGE => $productImageStorageTransfer->getIdProductImage(),
                        static::KEY_UPDATED_AT => '',
                        static::KEY_EXTERNAL_URL_SMALL => $productImageStorageTransfer->getExternalUrlSmall(),
                        static::KEY_EXTERNAL_URL_LARGE => $productImageStorageTransfer->getExternalUrlLarge(),
                        static::KEY_CREATED_AT => '',
                        static::KEY_ID_PRODUCT_IMAGE_SET_TO_PRODUCT_IMAGE => '',
                        static::KEY_SORT_ORDER => 0,
                        static::KEY_FK_PRODUCT_IMAGE => $productImageStorageTransfer->getIdProductImage(),
                    ];
                }
            }
        }

        return $elasticaImages;
    }
}
