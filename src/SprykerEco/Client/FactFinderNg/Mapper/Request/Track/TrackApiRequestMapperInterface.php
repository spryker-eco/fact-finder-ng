<?php

namespace SprykerEco\Client\FactFinderNg\Mapper\Request\Track;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TrackCheckoutRequestTransfer;

interface TrackApiRequestMapperInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return TrackCheckoutRequestTransfer
     */
    public function mapQuoteTransferToTrackCheckoutRequestTransfer(QuoteTransfer $quoteTransfer): TrackCheckoutRequestTransfer;
}
