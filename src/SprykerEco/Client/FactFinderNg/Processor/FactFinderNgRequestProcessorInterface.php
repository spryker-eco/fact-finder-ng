<?php

namespace SprykerEco\Client\FactFinderNg\Processor;

use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface FactFinderNgRequestProcessorInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return FactFinderNgResponseTransfer
     */
    public function processApiRequest(QuoteTransfer $quoteTransfer): FactFinderNgResponseTransfer;
}
