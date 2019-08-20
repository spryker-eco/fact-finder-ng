<?php


namespace SprykerEco\Client\FactFinderNg\Mapper\Request\Track;


use Generated\Shared\Transfer\CartOrCheckoutEventTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TrackCheckoutRequestTransfer;

class TrackApiRequestMapper implements TrackApiRequestMapperInterface
{
    /**
     * @param QuoteTransfer $quoteTransfer
     *
     * @return TrackCheckoutRequestTransfer
     */
    public function mapQuoteTransferToTrackCheckoutRequestTransfer(QuoteTransfer $quoteTransfer): TrackCheckoutRequestTransfer
    {
        $trackCheckoutRequestTransfer = new TrackCheckoutRequestTransfer();

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $cartOrCheckoutEventTransfer = new CartOrCheckoutEventTransfer();
            $cartOrCheckoutEventTransfer->setCount($itemTransfer->getQuantity());
            $cartOrCheckoutEventTransfer->setId($itemTransfer->getId());
            $cartOrCheckoutEventTransfer->setSid(uniqid());

            $trackCheckoutRequestTransfer->addEvent($cartOrCheckoutEventTransfer);
        }

        return $trackCheckoutRequestTransfer;
    }
}
