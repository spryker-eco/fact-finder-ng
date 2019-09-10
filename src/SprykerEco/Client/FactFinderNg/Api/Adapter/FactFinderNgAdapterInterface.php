<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Api\Adapter;

use Generated\Shared\Transfer\FactFinderNgRequestTransfer;
use Psr\Http\Message\ResponseInterface;

interface FactFinderNgAdapterInterface
{
    /**
     * @param \Generated\Shared\Transfer\FactFinderNgRequestTransfer $factFinderNgRequestTransfer
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sendRequest(FactFinderNgRequestTransfer $factFinderNgRequestTransfer): ResponseInterface;
}
