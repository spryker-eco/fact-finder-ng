<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Mapper\Request;

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
     * @return \Generated\Shared\Transfer\FactFinderNgRequestTransfer
     */
    public function mapNavigationRequest(array $requestParameters): FactFinderNgRequestTransfer;

    /**
     * @return \Generated\Shared\Transfer\FactFinderNgRequestTransfer
     */
    public function mapTriggerSearchImportRequest(): FactFinderNgRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\CartOrCheckoutEventTransfer[] $cartOrCheckoutEventTransfers
     *
     * @return \Generated\Shared\Transfer\FactFinderNgRequestTransfer
     */
    public function mapTrackCheckoutEventRequest(
        array $cartOrCheckoutEventTransfers
    ): FactFinderNgRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\CartOrCheckoutEventTransfer[] $cartOrCheckoutEventTransfers
     *
     * @return \Generated\Shared\Transfer\FactFinderNgRequestTransfer
     */
    public function mapTrackCartEventRequest(
        array $cartOrCheckoutEventTransfers
    ): FactFinderNgRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\ClickEventTransfer[] $clickEventTransfers
     *
     * @return \Generated\Shared\Transfer\FactFinderNgRequestTransfer
     */
    public function mapTrackClickEventRequest(
        array $clickEventTransfers
    ): FactFinderNgRequestTransfer;
}
