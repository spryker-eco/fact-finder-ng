<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Mapper\Request;

use Generated\Shared\Transfer\CartOrCheckoutEventTransfer;
use Generated\Shared\Transfer\ClickEventTransfer;
use Generated\Shared\Transfer\FactFinderNgRequestTransfer;

interface FactFinderNgRequestMapperInterface
{
    /**
     * @param array $requestParameters
     *
     * @return \Generated\Shared\Transfer\FactFinderNgRequestTransfer
     */
    public function mapSearchRequest(array $requestParameters): FactFinderNgRequestTransfer;

    /**
     * @param array $requestParameters
     *
     * @return \Generated\Shared\Transfer\FactFinderNgRequestTransfer
     */
    public function mapSuggestionRequest(array $requestParameters): FactFinderNgRequestTransfer;

    /**
     * @param array $requestParameters
     *
     * @return FactFinderNgRequestTransfer
     */
    public function mapNavigationRequest(array $requestParameters): FactFinderNgRequestTransfer;

    /**
     * @return FactFinderNgRequestTransfer
     */
    public function mapTriggerSearchImportRequest(): FactFinderNgRequestTransfer;

    /**
     * @param CartOrCheckoutEventTransfer[] $cartOrCheckoutEventTransfers
     *
     * @return FactFinderNgRequestTransfer
     */
    public function mapTrackCheckoutEventRequest(
        array $cartOrCheckoutEventTransfers
    ): FactFinderNgRequestTransfer;

    /**
     * @param CartOrCheckoutEventTransfer[] $cartOrCheckoutEventTransfers
     *
     * @return FactFinderNgRequestTransfer
     */
    public function mapTrackCartEventRequest(
        array $cartOrCheckoutEventTransfers
    ): FactFinderNgRequestTransfer;

    /**
     * @param ClickEventTransfer[] $clickEventTransfers
     *
     * @return FactFinderNgRequestTransfer
     */
    public function mapTrackClickEventRequest(
        array $clickEventTransfers
    ): FactFinderNgRequestTransfer;
}
