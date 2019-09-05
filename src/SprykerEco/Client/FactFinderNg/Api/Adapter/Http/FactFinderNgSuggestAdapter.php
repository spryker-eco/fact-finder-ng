<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Api\Adapter\Http;

use Generated\Shared\Transfer\FactFinderNgRequestTransfer;
use Symfony\Component\HttpFoundation\Request;

class FactFinderNgSuggestAdapter extends FactFinderAbstractAdapter
{
    protected const FACT_FINDER_URL_SUGGEST = 'suggest';

    /**
     * @param \Generated\Shared\Transfer\FactFinderNgRequestTransfer $factFinderNgRequestTransfer
     *
     * @return string
     */
    protected function getUrl(FactFinderNgRequestTransfer $factFinderNgRequestTransfer): string
    {
        return sprintf(
            '%s/%s/%s/%s',
            static::FACT_FINDER_URL_BASE,
            static::FACT_FINDER_URL_TYPE_URL,
            static::FACT_FINDER_URL_VERSION,
            static::FACT_FINDER_URL_SUGGEST
        );
    }

    /**
     * @return string
     */
    protected function getMethod(): string
    {
        return Request::METHOD_POST;
    }
}
