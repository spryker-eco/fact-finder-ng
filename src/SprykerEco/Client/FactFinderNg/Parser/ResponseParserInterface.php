<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Parser;

use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use Psr\Http\Message\ResponseInterface;

interface ResponseParserInterface
{
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function parseResponse(ResponseInterface $response): FactFinderNgResponseTransfer;
}
