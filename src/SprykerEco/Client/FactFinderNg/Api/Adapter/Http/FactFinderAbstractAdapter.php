<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Api\Adapter\Http;

use Generated\Shared\Transfer\FactFinderNgRequestTransfer;
use Generated\Shared\Transfer\FactFinderNgResponseErrorTransfer;
use Generated\Shared\Transfer\FactFinderNgResponseTransfer;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use SprykerEco\Client\FactFinderNg\Api\Adapter\FactFinderNgAdapterInterface;
use SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface;
use SprykerEco\Client\FactFinderNg\FactFinderNgConfig;

abstract class FactFinderAbstractAdapter implements FactFinderNgAdapterInterface
{
    protected const HEADER_KEY_CONTENT_TYPE = 'Content-Type';
    protected const HEADER_VALUE_APPLICATION_JSON = 'application/json';

    protected const FACT_FINDER_URL_BASE = 'http://mytheresa-ng.fact-finder.de/FACT-Finder';
    protected const FACT_FINDER_URL_TYPE_URL = 'rest';
    protected const FACT_FINDER_URL_VERSION = 'v2';
    protected const FACT_FINDER_URL_TRACK = 'track';

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    /**
     * @var \SprykerEco\Client\FactFinderNg\FactFinderNgConfig
     */
    protected $config;

    /**
     * @param \Generated\Shared\Transfer\FactFinderNgRequestTransfer $factFinderNgRequestTransfer
     *
     * @return string
     */
    abstract protected function getUrl(FactFinderNgRequestTransfer $factFinderNgRequestTransfer): string;

    /**
     * @return string
     */
    abstract protected function getMethod(): string;

    /**
     * @param \GuzzleHttp\ClientInterface $client
     * @param \SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface $utilEncodingService
     * @param \SprykerEco\Client\FactFinderNg\FactFinderNgConfig $config
     */
    public function __construct(
        ClientInterface $client,
        FactFinderNgToUtilEncodingServiceInterface $utilEncodingService,
        FactFinderNgConfig $config
    ) {
        $this->httpClient = $client;
        $this->utilEncodingService = $utilEncodingService;
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\FactFinderNgRequestTransfer $factFinderNgRequestTransfer
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    public function sendRequest(FactFinderNgRequestTransfer $factFinderNgRequestTransfer): FactFinderNgResponseTransfer
    {
        $url = $this->getUrl($factFinderNgRequestTransfer);
        $method = $this->getMethod();
        $options = $this->getOptions($factFinderNgRequestTransfer);

        return $this->send($method, $url, $options);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $options
     *
     * @return \Generated\Shared\Transfer\FactFinderNgResponseTransfer
     */
    protected function send(string $method, string $url, array $options = []): FactFinderNgResponseTransfer
    {
        $responseTransfer = new FactFinderNgResponseTransfer();

        try {
            $response = $this->httpClient->request($method, $url, $options);
            $responseTransfer->setBody($this->utilEncodingService->decodeJson($response->getBody(), true));
        } catch (GuzzleException $requestException) {
            $errorTransfer = new FactFinderNgResponseErrorTransfer();
            $errorTransfer->setErrorCode($requestException->getCode());
            $errorTransfer->setErrorMessage($requestException->getMessage());
            $responseTransfer->setError($errorTransfer);
        }

        return $responseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\FactFinderNgRequestTransfer $factFinderNgRequestTransfer
     *
     * @return array
     */
    protected function getOptions(FactFinderNgRequestTransfer $factFinderNgRequestTransfer): array
    {
        $options[RequestOptions::BODY] = $this->utilEncodingService->encodeJson($factFinderNgRequestTransfer->getPayload());
        $options[RequestOptions::HEADERS] = $this->getHeaders();
        $options[RequestOptions::AUTH] = $this->getAuth();

        return $options;
    }

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        return [
            static::HEADER_KEY_CONTENT_TYPE => static::HEADER_VALUE_APPLICATION_JSON,
        ];
    }

    /**
     * @return array
     */
    protected function getAuth(): array
    {
        return [
            $this->config->getFactFinderUsername(),
            $this->config->getFactFinderPassword(),
        ];
    }
}
