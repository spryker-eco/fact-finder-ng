<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Mapper\Request;

use Generated\Shared\Transfer\CartOrCheckoutEventTransfer;
use Generated\Shared\Transfer\ClickEventTransfer;
use Generated\Shared\Transfer\FactFinderNgRequestTransfer;
use SprykerEco\Client\FactFinderNg\FactFinderNgConfig;

class FactFinderNgRequestMapper implements FactFinderNgRequestMapperInterface
{
    public const KEY_PARAMS = 'params';
    public const KEY_CHANNEL = 'channel';
    public const KEY_QUERY = 'query';
    public const KEY_PAGE = 'page';
    public const KEY_HITS_PER_PAGE = 'hitsPerPage';
    public const KEY_SORT_ITEMS = 'sortItems';
    public const KEY_NAME = 'name';
    public const KEY_SORT_ORDER = 'order';
    public const KEY_FILTERS = 'filters';
    public const KEY_SUBSTRING = 'substring';
    public const KEY_VALUES = 'values';
    public const KEY_EXCLUDE = 'exclude';
    public const KEY_TYPE = 'type';
    public const KEY_VALUE = 'value';

    public const DEFAULT_VALUE_POS_PARAM = 1;
    public const DEFAULT_VALUE_PAGE_PARAM = 1;
    public const DEFAULT_VALUE_QUERY_PARAM = 'query';
    public const DEFAULT_VALUE_PAGE_SIZE_PARAM = 12;


    public const TYPE_OR = 'or';
    public const TYPE_AND = 'and';

    public const KEY_REQUEST_PARAMETER_Q = 'q';
    public const KEY_REQUEST_PARAMETER_PAGE = 'page';
    public const KEY_REQUEST_PARAMETER_IPP = 'ipp';
    public const KEY_REQUEST_PARAMETER_SORT = 'sort';
    public const KEY_REQUEST_PARAMETER_CATEGORY = 'category';

    public const DEFAULT_IPP_VALUE = 12;
    public const DEFAULT_PAGE_VALUE = 1;

    /**
     * @var \SprykerEco\Client\FactFinderNg\FactFinderNgConfig
     */
    protected $config;

    /**
     * @param \SprykerEco\Client\FactFinderNg\FactFinderNgConfig $config
     */
    public function __construct(
        FactFinderNgConfig $config
    ) {
        $this->config = $config;
    }

    /**
     * @param array $requestParameters
     *
     * @return \Generated\Shared\Transfer\FactFinderNgRequestTransfer
     */
    public function mapSearchRequest(array $requestParameters): FactFinderNgRequestTransfer
    {
        $params = [
            static::KEY_CHANNEL => $this->config->getFactFinderChannel(),
        ];

        $params = $this->addQueryParam($params, $requestParameters);
        $params = $this->addPageParam($params, $requestParameters);
        $params = $this->addHitsPerPageParam($params, $requestParameters);
        $params = $this->addSortItemsParam($params, $requestParameters);
        $params = $this->addFiltersParam($params, $requestParameters);

        $payload = [
            static::KEY_PARAMS => $params,
        ];

        $factFinderNgRequestTransfer = new FactFinderNgRequestTransfer();
        $factFinderNgRequestTransfer->setPayload($payload);

        return $factFinderNgRequestTransfer;
    }

    /**
     * @param array $requestParameters
     *
     * @return \Generated\Shared\Transfer\FactFinderNgRequestTransfer
     */
    public function mapSuggestionRequest(array $requestParameters): FactFinderNgRequestTransfer
    {
        $payload = [
            static::KEY_CHANNEL => $this->config->getFactFinderChannel(),
            static::KEY_QUERY => $requestParameters[static::KEY_REQUEST_PARAMETER_Q],
        ];

        $factFinderNgRequestTransfer = new FactFinderNgRequestTransfer();
        $factFinderNgRequestTransfer->setPayload($payload);

        return $factFinderNgRequestTransfer;
    }

    /**
     * @param array $requestParameters
     *
     * @return FactFinderNgRequestTransfer
     */
    public function mapNavigationRequest(array $requestParameters): FactFinderNgRequestTransfer
    {
        $params = [
            static::KEY_CHANNEL => $this->config->getFactFinderChannel(),
        ];

        $params = $this->addPageParam($params, $requestParameters);
        $params = $this->addHitsPerPageParam($params, $requestParameters);
        $params = $this->addSortItemsParam($params, $requestParameters);
        $params = $this->addFiltersParam($params, $requestParameters);

        $payload = [
            static::KEY_PARAMS => $params,
        ];

        $factFinderNgRequestTransfer = new FactFinderNgRequestTransfer();
        $factFinderNgRequestTransfer->setPayload($payload);

        return $factFinderNgRequestTransfer;
    }


    /**
     * @return FactFinderNgRequestTransfer
     */
    public function mapTriggerSearchImportRequest(): FactFinderNgRequestTransfer
    {
        $factFinderNgRequestTransfer = new FactFinderNgRequestTransfer();
        $factFinderNgRequestTransfer->setPayload([]);

        return $factFinderNgRequestTransfer;
    }


    /**
     * @param CartOrCheckoutEventTransfer[] $cartOrCheckoutEventTransfers
     *
     * @return FactFinderNgRequestTransfer
     */
    public function mapTrackCheckoutEventRequest(array $cartOrCheckoutEventTransfers): FactFinderNgRequestTransfer
    {
        $payload = [];

        foreach ($cartOrCheckoutEventTransfers as $cartOrCheckoutEventTransfer) {
            $cartOrCheckoutEventTransfer->requireSid();
            $cartOrCheckoutEventTransfer->requireId();

            $payload[] = $cartOrCheckoutEventTransfer->toArray(true, true);
        }

        $factFinderNgRequestTransfer = new FactFinderNgRequestTransfer();
        $factFinderNgRequestTransfer->setPayload($payload);

        return $factFinderNgRequestTransfer;
    }


    /**
     * @param CartOrCheckoutEventTransfer[] $cartOrCheckoutEventTransfers
     *
     * @return FactFinderNgRequestTransfer
     */
    public function mapTrackCartEventRequest(array $cartOrCheckoutEventTransfers): FactFinderNgRequestTransfer
    {
        $payload = [];

        foreach ($cartOrCheckoutEventTransfers as $cartOrCheckoutEventTransfer) {
            $cartOrCheckoutEventTransfer->requireSid();
            $cartOrCheckoutEventTransfer->requireId();

            $payload[] = $cartOrCheckoutEventTransfer->toArray(true, true);
        }

        $factFinderNgRequestTransfer = new FactFinderNgRequestTransfer();
        $factFinderNgRequestTransfer->setPayload($payload);

        return $factFinderNgRequestTransfer;
    }

    /**
     * @param ClickEventTransfer[] $clickEventTransfers
     *
     * @return FactFinderNgRequestTransfer
     */
    public function mapTrackClickEventRequest(array $clickEventTransfers): FactFinderNgRequestTransfer
    {
        $payload = [];

        foreach ($clickEventTransfers as $clickEventTransfer) {
            $clickEventTransfer->setPos($clickEventTransfer->getPos() ?? static::DEFAULT_VALUE_POS_PARAM);
            $clickEventTransfer->setPage($clickEventTransfer->getPage() ?? static::DEFAULT_VALUE_PAGE_PARAM);
            $clickEventTransfer->setQuery($clickEventTransfer->getQuery() ?? static::DEFAULT_VALUE_QUERY_PARAM);
            $clickEventTransfer->setOrigPos($clickEventTransfer->getOrigPos() ?? static::DEFAULT_VALUE_POS_PARAM);
            $clickEventTransfer->setOrigPageSize($clickEventTransfer->getOrigPageSize() ?? static::DEFAULT_VALUE_PAGE_SIZE_PARAM);

            $clickEventTransfer->requireSid();
            $clickEventTransfer->requireId();

            $payload[] = $clickEventTransfer->toArray(true, true);
        }

        $factFinderNgRequestTransfer = new FactFinderNgRequestTransfer();
        $factFinderNgRequestTransfer->setPayload($payload);

        return $factFinderNgRequestTransfer;
    }

    /**
     * @param array $params
     * @param array $requestParameters
     *
     * @return array
     */
    protected function addSortItemsParam(array $params, array $requestParameters): array
    {
        if (isset($requestParameters[static::KEY_REQUEST_PARAMETER_SORT]) && $requestParameters[static::KEY_REQUEST_PARAMETER_SORT]) {
            $name = explode('_', $requestParameters[static::KEY_REQUEST_PARAMETER_SORT])[0];
            $order = explode('_', $requestParameters[static::KEY_REQUEST_PARAMETER_SORT])[1];

            $params[static::KEY_SORT_ITEMS][] = [
                static::KEY_NAME => ucfirst($name),
                static::KEY_SORT_ORDER => $order,
            ];
        }

        return $params;
    }

    /**
     * @param array $params
     * @param array $requestParameters
     *
     * @return array
     */
    protected function addPageParam(array $params, array $requestParameters): array
    {
        $params[static::KEY_PAGE] = $requestParameters[static::KEY_REQUEST_PARAMETER_PAGE] ?? static::DEFAULT_PAGE_VALUE;

        return $params;
    }

    /**
     * @param array $params
     * @param array $requestParameters
     *
     * @return array
     */
    protected function addHitsPerPageParam(array $params, array $requestParameters): array
    {
        $params[static::KEY_HITS_PER_PAGE] = $requestParameters[static::KEY_REQUEST_PARAMETER_IPP] ?? static::DEFAULT_IPP_VALUE;

        return $params;
    }

    /**
     * @param array $params
     * @param array $requestParameters
     *
     * @return array
     */
    protected function addQueryParam(array $params, array $requestParameters): array
    {
        if (isset($requestParameters[static::KEY_REQUEST_PARAMETER_Q])) {
            $params[static::KEY_QUERY] = $requestParameters[static::KEY_REQUEST_PARAMETER_Q];
        }

        return $params;
    }

    /**
     * @param array $params
     * @param array $requestParameters
     *
     * @return array
     */
    protected function addFiltersParam(array $params, array $requestParameters): array
    {
        $baseParameters = [
            static::KEY_REQUEST_PARAMETER_IPP,
            static::KEY_REQUEST_PARAMETER_PAGE,
            static::KEY_REQUEST_PARAMETER_Q,
            static::KEY_REQUEST_PARAMETER_SORT,
            static::KEY_REQUEST_PARAMETER_CATEGORY,
        ];

        $filters = [];

        foreach ($requestParameters as $key => $value) {
            if (in_array($key, $baseParameters) || is_array($value) && (isset($value[0]['min']) || isset($value[0]['max']))) {
                continue;
            }

            $filters[] = [
                static::KEY_NAME => $key,
                static::KEY_SUBSTRING => false,
                static::KEY_VALUES => is_array($value) ? $this->getMultipleFilterValues($value) : $this->getSingleFilterValues($value),
            ];
        }

        if ($filters) {
            $params[static::KEY_FILTERS] = $filters;
        }

        return $params;
    }

    /**
     * @param array $values
     *
     * @return array
     */
    protected function getMultipleFilterValues(array $values): array
    {
        $filterValues = [];
        foreach ($values as $value) {
            $filterValues[] = [
                static::KEY_EXCLUDE => false,
                static::KEY_TYPE => static::TYPE_OR,
                static::KEY_VALUE => $value,
            ];
        }

        return $filterValues;
    }

    /**
     * @param mixed $value
     *
     * @return array
     */
    protected function getSingleFilterValues($value): array
    {
        $filterValues[] = [
            static::KEY_EXCLUDE => false,
            static::KEY_TYPE => static::TYPE_AND,
            static::KEY_VALUE => $value,
        ];

        return $filterValues;
    }
}
