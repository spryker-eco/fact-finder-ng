<?php

namespace SprykerEco\Client\FactFinderNg\ImportTrigger;

use Generated\Shared\Transfer\FactFinderNgResponseTransfer;

interface ImportTriggerInterface
{
    /**
     * @return FactFinderNgResponseTransfer
     */
    public function trigger(): FactFinderNgResponseTransfer;
}
