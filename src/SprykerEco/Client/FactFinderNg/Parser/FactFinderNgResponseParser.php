<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Parser;

use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use Generated\Shared\Transfer\FactFinderNgSearchResponseTransfer;
use Generated\Shared\Transfer\FactFinderNgSuggestionResponseTransfer;
use Generated\Shared\Transfer\FactFinderNgTrackCheckoutResponseTransfer;

class FactFinderNgResponseParser implements FactFinderNgResponseParserInterface
{
    /**
     * @param \Generated\Shared\Transfer\FactFinderNgResponseTransfer $factFinderNgResponseTransfer
     *
     * @return \Generated\Shared\Transfer\FactFinderNgSearchResponseTransfer
     */
    public function parseSearchResponse(FactFinderNgResponseTransfer $factFinderNgResponseTransfer): FactFinderNgSearchResponseTransfer
    {
        $transfer = new FactFinderNgSearchResponseTransfer();
        $transfer->setBody($factFinderNgResponseTransfer->getBody());

        return $transfer;
    }

    /**
     * @param \Generated\Shared\Transfer\FactFinderNgResponseTransfer $factFinderNgResponseTransfer
     *
     * @return \Generated\Shared\Transfer\FactFinderNgSuggestionResponseTransfer
     */
    public function parseSuggestionResponse(FactFinderNgResponseTransfer $factFinderNgResponseTransfer): FactFinderNgSuggestionResponseTransfer
    {
        $transfer = new FactFinderNgSuggestionResponseTransfer();
        $transfer->setBody($factFinderNgResponseTransfer->getBody());

        return $transfer;
    }

    /**
     * @param FactFinderNgResponseTransfer $factFinderNgResponseTransfer
     *
     * @return FactFinderNgTrackCheckoutResponseTransfer
     */
    public function parseTrackCheckoutResponse(FactFinderNgResponseTransfer $factFinderNgResponseTransfer): FactFinderNgTrackCheckoutResponseTransfer
    {
        $transfer = new FactFinderNgTrackCheckoutResponseTransfer();
        $transfer->setBody($factFinderNgResponseTransfer->getBody());

        return $transfer;
    }
}
