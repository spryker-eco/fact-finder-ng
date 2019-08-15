<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Api\RequestSender;

use Elastica\Query;
use Generated\Shared\Transfer\FactFinderNgSearchResponseTransfer;
use Generated\Shared\Transfer\FactFinderNgSuggestionResponseTransfer;

interface RequestSenderInterface
{
    /**
     * @param \Elastica\Query $query
     * @param array $requestParameters
     *
     * @return \Generated\Shared\Transfer\FactFinderNgSearchResponseTransfer
     */
    public function sendSearchRequest(Query $query, array $requestParameters): FactFinderNgSearchResponseTransfer;

    /**
     * @param \Elastica\Query $query
     * @param array $requestParameters
     *
     * @return \Generated\Shared\Transfer\FactFinderNgSuggestionResponseTransfer
     */
    public function sendSuggestionRequest(Query $query, array $requestParameters): FactFinderNgSuggestionResponseTransfer;
}
