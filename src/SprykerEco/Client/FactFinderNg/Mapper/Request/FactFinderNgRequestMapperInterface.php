<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Mapper\Request;

use Generated\Shared\Transfer\FactFinderNgRequestTransfer;

interface FactFinderNgRequestMapperInterface
{
    /**
     * @param array $requestParameters
     *
     * @return \Generated\Shared\Transfer\FactFinderNgRequestTransfer
     */
    public function mapSearchRequest(array $requestParameters): FactFinderNgRequestTransfer;

    /**
     * @param array $requestParameters
     *
     * @return \Generated\Shared\Transfer\FactFinderNgRequestTransfer
     */
    public function mapSuggestionRequest(array $requestParameters): FactFinderNgRequestTransfer;

    /**
     * @param array $requestParameters
     *
     * @return FactFinderNgRequestTransfer
     */
    public function mapTrackCheckoutRequest(array $requestParameters): FactFinderNgRequestTransfer;
}
