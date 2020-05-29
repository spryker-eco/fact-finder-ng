<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Handler;

use Elastica\Query;
use Elastica\ResultSet;
use Exception;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;
use Spryker\Client\Search\Exception\SearchResponseException;
use Spryker\Client\Search\Model\Handler\SearchHandlerInterface;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface;
use SprykerEco\Client\FactFinderNg\Dependency\Client\FactFinderNgToLocaleClientInterface;
use SprykerEco\Client\FactFinderNg\Dependency\Client\FactFinderNgToStoreClientInterface;
use SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface;
use SprykerEco\Client\FactFinderNg\Mapper\Elastica\FactFinderToElasticaMapperInterface;
use SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface;
use SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface;

abstract class FactFinderHandler implements SearchHandlerInterface
{
    /**
     * @var \SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface
     */
    protected $requestMapper;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface
     */
    protected $adapterFactory;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface
     */
    protected $responseParser;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Mapper\Elastica\FactFinderToElasticaMapperInterface
     */
    protected $factFinderToElasticaMapper;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Dependency\Client\FactFinderNgToLocaleClientInterface
     */
    protected $localeClient;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Dependency\Client\FactFinderNgToStoreClientInterface
     */
    protected $storeClient;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    /**
     * @param \SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface $factFinderNgRequestMapper
     * @param \SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface $adapterFactory
     * @param \SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface $responseParser
     * @param \SprykerEco\Client\FactFinderNg\Mapper\Elastica\FactFinderToElasticaMapperInterface $factFinderToElasticaMapper
     * @param \SprykerEco\Client\FactFinderNg\Dependency\Client\FactFinderNgToLocaleClientInterface $localeClient
     * @param \SprykerEco\Client\FactFinderNg\Dependency\Client\FactFinderNgToStoreClientInterface $storeClient
     * @param \SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(
        FactFinderNgRequestMapperInterface $factFinderNgRequestMapper,
        AdapterFactoryInterface $adapterFactory,
        ResponseParserInterface $responseParser,
        FactFinderToElasticaMapperInterface $factFinderToElasticaMapper,
        FactFinderNgToLocaleClientInterface $localeClient,
        FactFinderNgToStoreClientInterface $storeClient,
        FactFinderNgToUtilEncodingServiceInterface $utilEncodingService
    ) {
        $this->requestMapper = $factFinderNgRequestMapper;
        $this->adapterFactory = $adapterFactory;
        $this->responseParser = $responseParser;
        $this->factFinderToElasticaMapper = $factFinderToElasticaMapper;
        $this->localeClient = $localeClient;
        $this->storeClient = $storeClient;
        $this->utilEncodingService = $utilEncodingService;
    }

    /**
     * @param \Spryker\Client\Search\Dependency\Plugin\QueryInterface $searchQuery
     * @param \Spryker\Client\Search\Dependency\Plugin\ResultFormatterPluginInterface[] $resultFormatters
     * @param array $requestParameters
     *
     * @return array|\Elastica\ResultSet
     */
    public function search(
        QueryInterface $searchQuery,
        array $resultFormatters = [],
        array $requestParameters = []
    ) {
        $elasticaQuery = $searchQuery->getSearchQuery();
        $searchResult = $this->executeQuery($elasticaQuery, $requestParameters);
        $elasticaSearchResult = $this->factFinderToElasticaMapper->map(
            $searchResult,
            $elasticaQuery,
            $this->localeClient->getCurrentLocale(),
            $this->storeClient->getCurrentStore()
        );

        if (!$resultFormatters) {
            return $elasticaSearchResult;
        }

        return $this->formatSearchResults($resultFormatters, $elasticaSearchResult, $requestParameters);
    }

    /**
     * @param \Spryker\Client\Search\Dependency\Plugin\ResultFormatterPluginInterface[] $resultFormatters
     * @param \Elastica\ResultSet $rawSearchResult
     * @param array $requestParameters
     *
     * @return array
     */
    protected function formatSearchResults(
        array $resultFormatters,
        ResultSet $rawSearchResult,
        array $requestParameters
    ): array {
        $formattedSearchResult = [];

        foreach ($resultFormatters as $resultFormatter) {
            $formattedSearchResult[$resultFormatter->getName()] = $resultFormatter->formatResult($rawSearchResult, $requestParameters);
        }

        return $formattedSearchResult;
    }

    /**
     * @param \Exception $exception
     * @param \Elastica\Query $query
     *
     * @throws \Spryker\Client\Search\Exception\SearchResponseException
     *
     * @return void
     */
    protected function throwSearchException(Exception $exception, Query $query): void
    {
        $rawQuery = $this->utilEncodingService->encodeJson($query->toArray());

        throw new SearchResponseException(
            sprintf('Search failed with the following reason: %s. Query: %s', $exception->getMessage(), $rawQuery),
            $exception->getCode(),
            $exception
        );
    }

    /**
     * @param \Elastica\Query $query
     * @param array $requestParameters
     *
     * @throws \Spryker\Client\Search\Exception\SearchResponseException
     *
     * @return array
     */
    abstract protected function executeQuery(Query $query, array $requestParameters): array;
}
