<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Dependency\Client;

use Generated\Shared\Transfer\ProductAbstractImageStorageTransfer;

class FactFinderNgToProductImageStorageClientBridge implements FactFinderNgToProductImageStorageClientInterface
{
    /**
     * @var \Spryker\Client\ProductImageStorage\ProductImageStorageClientInterface
     */
    protected $productImageStorageClient;

    /**
     * @param \Spryker\Client\ProductImageStorage\ProductImageStorageClientInterface $productImageStorageClient
     */
    public function __construct($productImageStorageClient)
    {
        $this->productImageStorageClient = $productImageStorageClient;
    }

    /**
     * @param int $idProductAbstract
     * @param string $locale
     *
     * @return \Generated\Shared\Transfer\ProductAbstractImageStorageTransfer|null
     */
    public function findProductImageAbstractStorageTransfer(int $idProductAbstract, string $locale): ?ProductAbstractImageStorageTransfer
    {
        return $this->productImageStorageClient->findProductImageAbstractStorageTransfer($idProductAbstract, $locale);
    }
}
