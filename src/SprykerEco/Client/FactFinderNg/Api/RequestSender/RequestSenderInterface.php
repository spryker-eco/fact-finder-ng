<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Api\RequestSender;

use Elastica\Query;
use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use Generated\Shared\Transfer\FactFinderNgSuggestionResponseTransfer;
use Generated\Shared\Transfer\TrackCheckoutRequestTransfer;
use Psr\Http\Message\ResponseInterface;

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
     * @return \Generated\Shared\Transfer\FactFinderNgSuggestionResponseTransfer
     */
    public function sendSuggestionRequest(Query $query, array $requestParameters): FactFinderNgSuggestionResponseTransfer;

    /**
     * @param TrackCheckoutRequestTransfer $trackCheckoutRequestTransfer
     *
     * @return FactFinderNgResponseTransfer
     */
    public function sendTrackCheckoutRequest(TrackCheckoutRequestTransfer $trackCheckoutRequestTransfer): FactFinderNgResponseTransfer;
}
