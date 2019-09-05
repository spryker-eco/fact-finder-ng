<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg;

use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

interface FactFinderNgClientInterface
{
    /**
     * Specification:
     * - Method send request to Fact finder for getting search result.
     * - Request parameters will be mapped to Fact Finder search parameters.
     *
     * @api
     *
     * @param \Spryker\Client\Search\Dependency\Plugin\QueryInterface $searchQuery
     * @param \Spryker\Client\Search\Dependency\Plugin\ResultFormatterPluginInterface[] $resultFormatters
     * @param array $requestParameters
     *
     * @return array|\Elastica\ResultSet
     */
    public function search(QueryInterface $searchQuery, array $resultFormatters = [], array $requestParameters = []);

    /**
     * Specification:
     * - Method send request to Fact finder for getting suggestion result.
     * - Request parameters will be mapped to Fact Finder suggestion parameters.
     *
     * @api
     *
     * @param \Spryker\Client\Search\Dependency\Plugin\QueryInterface $searchQuery
     * @param \Spryker\Client\Search\Dependency\Plugin\ResultFormatterPluginInterface[] $resultFormatters
     * @param array $requestParameters
     *
     * @return array|\Elastica\ResultSet
     */
    public function suggest(QueryInterface $searchQuery, array $resultFormatters = [], array $requestParameters = []);

    /**
     * Specification:
     * - Method send request to Fact finder for navigation results.
     * - Request parameters will be mapped to Fact Finder suggestion parameters.
     *
     * @api
     *
     * @param \Spryker\Client\Search\Dependency\Plugin\QueryInterface $searchQuery
     * @param \Spryker\Client\Search\Dependency\Plugin\ResultFormatterPluginInterface[] $resultFormatters
     * @param array $requestParameters
     *
     * @return array|\Elastica\ResultSet
     */
    public function navigation(QueryInterface $searchQuery, array $resultFormatters = [], array $requestParameters = []);

    /**
     * Specification:
     * - Method send request to Fact finder for tracking checkout completed event.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CartOrCheckoutEventTransfer[] $cartOrCheckoutEventTransfers
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function trackCheckoutEvent(array $cartOrCheckoutEventTransfers): FactFinderNgResponseTransfer;

    /**
     * Specification:
     * - Method send request to Fact finder for tracking adding to cart event.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CartOrCheckoutEventTransfer[] $cartOrCheckoutEventTransfers
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function trackCartEvent(array $cartOrCheckoutEventTransfers): FactFinderNgResponseTransfer;

    /**
     * Specification:
     * - Method send request to Fact finder for tracking clicking by product event.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ClickEventTransfer[] $clickEventTransfers
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function trackClickEvent(array $clickEventTransfers): FactFinderNgResponseTransfer;

    /**
     * Specification:
     * - Trigger importing search data via API call.
     * - Url for getting file is set in Fact Finder UI.
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function triggerSearchImport(): FactFinderNgResponseTransfer;
}
