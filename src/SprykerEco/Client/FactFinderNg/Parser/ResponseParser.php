<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Parser;

use Generated\Shared\Transfer\FactFinderNgResponseErrorTransfer;
use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
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
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
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
            $responseTransfer->setError($errorTransfer);

            return $responseTransfer;
        }

        $responseTransfer->setBody($responseBody);

        return $responseTransfer;
    }
}
