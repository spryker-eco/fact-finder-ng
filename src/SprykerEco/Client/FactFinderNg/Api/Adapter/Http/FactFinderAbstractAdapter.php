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
use Psr\Http\Message\ResponseInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Client\FactFinderNg\Api\Adapter\FactFinderNgAdapterInterface;
use SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface;
use SprykerEco\Client\FactFinderNg\FactFinderNgConfig;
use SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapper;

abstract class FactFinderAbstractAdapter implements FactFinderNgAdapterInterface
{
    protected const HEADER_KEY_CONTENT_TYPE = 'Content-Type';
    protected const HEADER_VALUE_APPLICATION_JSON = 'application/json';

    protected const FACT_FINDER_URL_BASE = 'https://fischer-duebel.fact-finder.de/fact-finder';
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
     * @param \Generated\Shared\Transfer\FactFinderNgRequestTransfer|AbstractTransfer $factFinderNgRequestTransfer
     *
     * @return ResponseInterface
     */
    public function sendRequest(AbstractTransfer $factFinderNgRequestTransfer): ResponseInterface
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
     * @return ResponseInterface
     */
    protected function send(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->httpClient->request($method, $url, $options);
    }

    /**
     * @param FactFinderNgRequestTransfer|AbstractTransfer $factFinderNgRequestTransfer
     *
     * @return array
     */
    protected function getOptions(AbstractTransfer $factFinderNgRequestTransfer): array
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
