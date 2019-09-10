<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg;

use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use Spryker\Client\Kernel\AbstractClient;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;

/**
 * @method \SprykerEco\Client\FactFinderNg\FactFinderNgFactory getFactory()
 */
class FactFinderNgClient extends AbstractClient implements FactFinderNgClientInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Spryker\Client\Search\Dependency\Plugin\QueryInterface $searchQuery
     * @param \Spryker\Client\Search\Dependency\Plugin\ResultFormatterPluginInterface[] $resultFormatters
     * @param array $requestParameters
     *
     * @return array|\Elastica\ResultSet
     */
    public function search(QueryInterface $searchQuery, array $resultFormatters = [], array $requestParameters = [])
    {
        return $this->getFactory()->createSearchHandler()->search($searchQuery, $resultFormatters, $requestParameters);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Spryker\Client\Search\Dependency\Plugin\QueryInterface $searchQuery
     * @param \Spryker\Client\Search\Dependency\Plugin\ResultFormatterPluginInterface[] $resultFormatters
     * @param array $requestParameters
     *
     * @return array|\Elastica\ResultSet
     */
    public function suggest(QueryInterface $searchQuery, array $resultFormatters = [], array $requestParameters = [])
    {
        return $this->getFactory()->createSuggestHandler()->search($searchQuery, $resultFormatters, $requestParameters);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Spryker\Client\Search\Dependency\Plugin\QueryInterface $searchQuery
     * @param array $resultFormatters
     * @param array $requestParameters
     *
     * @return array|\Elastica\ResultSet|void
     */
    public function navigation(QueryInterface $searchQuery, array $resultFormatters = [], array $requestParameters = [])
    {
        return $this->getFactory()->createNavigationHandler()->search($searchQuery, $resultFormatters, $requestParameters);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CartOrCheckoutEventTransfer[] $cartOrCheckoutEventTransfers
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function trackCheckoutEvent(array $cartOrCheckoutEventTransfers): FactFinderNgResponseTransfer
    {
        return $this->getFactory()->createCheckoutEventTracker()->track($cartOrCheckoutEventTransfers);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CartOrCheckoutEventTransfer[] $cartOrCheckoutEventTransfers
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function trackCartEvent(array $cartOrCheckoutEventTransfers): FactFinderNgResponseTransfer
    {
        return $this->getFactory()->createCartEventTracker()->track($cartOrCheckoutEventTransfers);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ClickEventTransfer[] $clickEventTransfers
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function trackClickEvent(array $clickEventTransfers): FactFinderNgResponseTransfer
    {
        return $this->getFactory()->createClickEventTracker()->track($clickEventTransfers);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function triggerSearchImport(): FactFinderNgResponseTransfer
    {
        return $this->getFactory()->createSearchImportTrigger()->trigger();
    }
}
