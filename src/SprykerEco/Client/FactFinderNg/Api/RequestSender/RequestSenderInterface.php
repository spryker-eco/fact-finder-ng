<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Api\RequestSender;

use Elastica\Query;
use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use Generated\Shared\Transfer\TrackCheckoutRequestTransfer;

interface RequestSenderInterface
{
    /**
     * @param \Elastica\Query $query
     * @param array $requestParameters
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function sendSearchRequest(Query $query, array $requestParameters): FactFinderNgResponseTransfer;

    /**
     * @param \Elastica\Query $query
     * @param array $requestParameters
     *
     * @return FactFinderNgResponseTransfer
     */
    public function sendSuggestionRequest(Query $query, array $requestParameters): FactFinderNgResponseTransfer;

    /**
     * @param TrackCheckoutRequestTransfer $trackCheckoutRequestTransfer
     *
     * @return FactFinderNgResponseTransfer
     */
    public function sendTrackCheckoutRequest(TrackCheckoutRequestTransfer $trackCheckoutRequestTransfer): FactFinderNgResponseTransfer;
}
