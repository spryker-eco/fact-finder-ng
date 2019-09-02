<?php

namespace SprykerEco\Client\FactFinderNg\Api\Adapter\Http;

use Generated\Shared\Transfer\FactFinderNgRequestTransfer;
use SprykerEco\Client\FactFinderNg\Mapper\Request\FactFinderNgRequestMapper;
use Symfony\Component\HttpFoundation\Request;

class FactFinderNgTrackCartAdapter extends FactFinderAbstractAdapter
{
    protected const FACT_FINDER_URL_CART = 'cart';

    /**
     * @param \Generated\Shared\Transfer\FactFinderNgRequestTransfer $factFinderNgRequestTransfer
     *
     * @return string
     */
    protected function getUrl(FactFinderNgRequestTransfer $factFinderNgRequestTransfer): string
    {
        return sprintf('%s/%s/%s/%s/%s/%s',
            static::FACT_FINDER_URL_BASE,
            static::FACT_FINDER_URL_TYPE_URL,
            static::FACT_FINDER_URL_VERSION,
            static::FACT_FINDER_URL_TRACK,
            $this->getChannel($factFinderNgRequestTransfer),
            static::FACT_FINDER_URL_CART
        );
    }

    /**
     * @return string
     */
    protected function getMethod(): string
    {
        return Request::METHOD_POST;
    }

    /**
     * @param FactFinderNgRequestTransfer $factFinderNgRequestTransfer
     *
     * @return string
     */
    protected function getChannel(FactFinderNgRequestTransfer $factFinderNgRequestTransfer): string
    {
        return $factFinderNgRequestTransfer->getPayload()[FactFinderNgRequestMapper::KEY_PARAMS][FactFinderNgRequestMapper::KEY_CHANNEL];
    }
}