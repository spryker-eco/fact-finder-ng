<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\ImportTrigger;

use Generated\Shared\Transfer\FactFinderNgResponseTransfer;

interface ImportTriggerInterface
{
    /**
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function trigger(): FactFinderNgResponseTransfer;
}
