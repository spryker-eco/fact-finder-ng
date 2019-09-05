<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Api\Adapter\Http;

use Generated\Shared\Transfer\FactFinderNgRequestTransfer;
use Symfony\Component\HttpFoundation\Request;

class FactFinderNgTrackCheckoutAdapter extends FactFinderAbstractAdapter
{
    protected const FACT_FINDER_URL_CHECKOUT = 'checkout';

    /**
     * @param \Generated\Shared\Transfer\FactFinderNgRequestTransfer $factFinderNgRequestTransfer
     *
     * @return string
     */
    protected function getUrl(FactFinderNgRequestTransfer $factFinderNgRequestTransfer): string
    {
        return sprintf(
            '%s/%s/%s/%s/%s/%s',
            $this->config->getFactFinderUrl(),
            static::FACT_FINDER_URL_TYPE_URL,
            static::FACT_FINDER_URL_VERSION,
            static::FACT_FINDER_URL_TRACK,
            $this->getChannel(),
            static::FACT_FINDER_URL_CHECKOUT
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
     * @return string
     */
    protected function getChannel(): string
    {
        return $this->config->getFactFinderChannel();
    }
}
