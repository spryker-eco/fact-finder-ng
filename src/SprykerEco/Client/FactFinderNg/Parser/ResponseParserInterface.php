<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Parser;

use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use Psr\Http\Message\ResponseInterface;

interface ResponseParserInterface
{
    /**
     * @param ResponseInterface $response
     *
     * @return FactFinderNgResponseTransfer
     */
    public function parseResponse(ResponseInterface $response): FactFinderNgResponseTransfer;
}
