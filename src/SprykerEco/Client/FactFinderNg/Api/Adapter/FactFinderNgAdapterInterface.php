<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Api\Adapter;

use Psr\Http\Message\ResponseInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

interface FactFinderNgAdapterInterface
{
    /**
     * @param AbstractTransfer $factFinderNgRequestTransfer
     *
     * @return ResponseInterface
     */
    public function sendRequest(AbstractTransfer $factFinderNgRequestTransfer): ResponseInterface;
}
