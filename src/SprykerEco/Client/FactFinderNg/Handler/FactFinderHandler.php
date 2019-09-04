<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Handler;

use Elastica\Query;
use Elastica\ResultSet;
use Spryker\Client\Locale\LocaleClientInterface;
use Spryker\Client\Search\Dependency\Plugin\QueryInterface;
use Spryker\Client\Search\Model\Handler\SearchHandlerInterface;
use Spryker\Client\Store\StoreClientInterface;
use SprykerEco\Client\FactFinderNg\Api\Adapter\Http\Factory\AdapterFactoryInterface;
use SprykerEco\Client\FactFinderNg\Dependency\Service\FactFinderNgToUtilEncodingServiceInterface;
use SprykerEco\Client\FactFinderNg\Mapper\Elastica\FactFinderToElasticaMapperInterface;
use SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapperInterface;
use SprykerEco\Client\FactFinderNg\Parser\ResponseParserInterface;

abstract class FactFinderHandler implements SearchHandlerInterface
{
    /**
     * @var FactFinderNgRequestMapperInterface
     */
    protected $requestMapper;

    /**
     * @var AdapterFactoryInterface
     */
    protected $adapterFactory;

    /**
     * @var ResponseParserInterface
     */
    protected $responseParser;

    /**
     * @var \SprykerEco\Client\FactFinderNg\Mapper\Elastica\FactFinderToElasticaMapperInterface
     */
    protected $factFinderToElasticaMapper;

    /**
     * @var \Spryker\Client\Locale\LocaleClientInterface
     */
    protected $localeClient;

    /**
     * @var \Spryker\Client\Store\StoreClientInterface
     */
    protected $storeClient;

    /**
     * @var FactFinderNgToUtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    /**
     * @param FactFinderNgRequestMapperInterface $factFinderNgRequestMapper
     * @param AdapterFactoryInterface $adapterFactory
     * @param ResponseParserInterface $responseParser
     * @param \SprykerEco\Client\FactFinderNg\Mapper\Elastica\FactFinderToElasticaMapperInterface $factFinderToElasticaMapper
     * @param \Spryker\Client\Locale\LocaleClientInterface $localeClient
     * @param \Spryker\Client\Store\StoreClientInterface $storeClient
     * @param FactFinderNgToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(
        FactFinderNgRequestMapperInterface $factFinderNgRequestMapper,
        AdapterFactoryInterface $adapterFactory,
        ResponseParserInterface $responseParser,
        FactFinderToElasticaMapperInterface $factFinderToElasticaMapper,
        LocaleClientInterface $localeClient,
        StoreClientInterface $storeClient,
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
     * @param \Elastica\Query $query
     * @param array $requestParameters
     *
     * @throws \Spryker\Client\Search\Exception\SearchResponseException
     *
     * @return array
     */
    abstract protected function executeQuery(Query $query, array $requestParameters): array;
}
