<?php

/**
 * This file is part of the Spryker Suite.
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
                        'fk_product_image_set' => '',
                        'id_product_image' => $productImageStorageTransfer->getIdProductImage(),
                        'updated_at' => '',
                        'external_url_small' => $productImageStorageTransfer->getExternalUrlSmall(),
                        'external_url_large' => $productImageStorageTransfer->getExternalUrlLarge(),
                        'created_at' => '',
                        'id_product_image_set_to_product_image' => '',
                        'sort_order' => 0,
                        'fk_product_image' => $productImageStorageTransfer->getIdProductImage(),
                    ];
                }
            }
        }
        return $elasticaImages;
    }
}
