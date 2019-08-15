<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Api\Adapter;

use Generated\Shared\Transfer\FactFinderNgRequestTransfer;
use Generated\Shared\Transfer\FactFinderNgResponseTransfer;

interface FactFinderNgAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\FactFinderNgRequestTransfer $factFinderNgRequestTransfer
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function sendRequest(FactFinderNgRequestTransfer $factFinderNgRequestTransfer): FactFinderNgResponseTransfer;
}
