<?php

namespace SprykerEco\Client\FactFinderNg\EventTracker;

use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use Spryker\Shared\Kernel\Transfer\TransferInterface;

interface EventTrackerInterface
{
    /**
     * @param TransferInterface[] $eventTransfers
     *
     * @return FactFinderNgResponseTransfer
     */
    public function track(array $eventTransfers): FactFinderNgResponseTransfer;
}
