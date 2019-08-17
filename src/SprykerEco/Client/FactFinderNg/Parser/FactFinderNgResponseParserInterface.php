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

interface FactFinderNgResponseParserInterface
{
    /**
     * @param \Generated\Shared\Transfer\FactFinderNgResponseTransfer $factFinderNgResponseTransfer
     *
     * @return \Generated\Shared\Transfer\FactFinderNgSearchResponseTransfer
     */
    public function parseSearchResponse(FactFinderNgResponseTransfer $factFinderNgResponseTransfer): FactFinderNgSearchResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\FactFinderNgResponseTransfer $factFinderNgResponseTransfer
     *
     * @return \Generated\Shared\Transfer\FactFinderNgSuggestionResponseTransfer
     */
    public function parseSuggestionResponse(FactFinderNgResponseTransfer $factFinderNgResponseTransfer): FactFinderNgSuggestionResponseTransfer;

    /**
     * @param FactFinderNgResponseTransfer $factFinderNgResponseTransfer
     *
     * @return FactFinderNgTrackCheckoutResponseTransfer
     */
    public function parseTrackCheckoutResponse(FactFinderNgResponseTransfer $factFinderNgResponseTransfer): FactFinderNgTrackCheckoutResponseTransfer;
}
