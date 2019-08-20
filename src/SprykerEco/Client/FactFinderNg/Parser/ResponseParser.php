<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Parser;

use Generated\Shared\Transfer\FactFinderNgResponseErrorTransfer;
use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use Generated\Shared\Transfer\FactFinderNgSearchResponseTransfer;
use Generated\Shared\Transfer\FactFinderNgSuggestionResponseTransfer;
use Generated\Shared\Transfer\FactFinderNgTrackCheckoutResponseTransfer;
use Psr\Http\Message\ResponseInterface;
use SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface;

class ResponseParser implements ResponseParserInterface
{
    public const RESPONSE_KEY_ERROR = 'error';
    public const RESPONSE_KEY_ERROR_DESCRIPTION = 'errorDescription';
    public const RESPONSE_KEY_ERROR_STACKTRACE = 'stacktrace';

    /**
     * @var \SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    /**
     * @param \SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(FactFinderNgToUtilEncodingServiceInterface $utilEncodingService)
    {
        $this->utilEncodingService = $utilEncodingService;
    }

    /**
     * @param \Generated\Shared\Transfer\FactFinderNgResponseTransfer $factFinderNgResponseTransfer
     *
     * @return \Generated\Shared\Transfer\FactFinderNgSearchResponseTransfer
     */
    public function parseSearchResponse(FactFinderNgResponseTransfer $factFinderNgResponseTransfer): FactFinderNgSearchResponseTransfer
    {
        $transfer = new FactFinderNgSearchResponseTransfer();
        $transfer->setBody($factFinderNgResponseTransfer->getBody());

        return $transfer;
    }

    /**
     * @param \Generated\Shared\Transfer\FactFinderNgResponseTransfer $factFinderNgResponseTransfer
     *
     * @return \Generated\Shared\Transfer\FactFinderNgSuggestionResponseTransfer
     */
    public function parseSuggestionResponse(FactFinderNgResponseTransfer $factFinderNgResponseTransfer): FactFinderNgSuggestionResponseTransfer
    {
        $transfer = new FactFinderNgSuggestionResponseTransfer();
        $transfer->setBody($factFinderNgResponseTransfer->getBody());

        return $transfer;
    }

    /**
     * @param FactFinderNgResponseTransfer $factFinderNgResponseTransfer
     *
     * @return FactFinderNgTrackCheckoutResponseTransfer
     */
    public function parseTrackCheckoutResponse(FactFinderNgResponseTransfer $factFinderNgResponseTransfer): FactFinderNgTrackCheckoutResponseTransfer
    {
        $transfer = new FactFinderNgTrackCheckoutResponseTransfer();
        $transfer->setBody($factFinderNgResponseTransfer->getBody());

        return $transfer;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return FactFinderNgResponseTransfer
     */
    public function parseResponse(ResponseInterface $response): FactFinderNgResponseTransfer
    {
        $responseTransfer = new FactFinderNgResponseTransfer();
        $responseBody = $this->utilEncodingService->decodeJson($response->getBody(), true);

        if ($response->getStatusCode() >= 400) {

            $errorTransfer = new FactFinderNgResponseErrorTransfer();
            $errorTransfer->setError($responseBody[static::RESPONSE_KEY_ERROR]);
            $errorTransfer->setErrorDescription($responseBody[static::RESPONSE_KEY_ERROR_DESCRIPTION]);
            $errorTransfer->setStacktrace($responseBody[static::RESPONSE_KEY_ERROR_STACKTRACE]);

            $responseTransfer->setIsSuccess(false);

            return $responseTransfer;
        }

        $responseTransfer->setBody($responseBody);

        return $responseTransfer;
    }
}
