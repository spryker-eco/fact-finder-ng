<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\FactFinderNg\Api\Adapter\Http;

use Generated\Shared\Transfer\FactFinderNgRequestTransfer;
use Symfony\Component\HttpFoundation\Request;

class FactFinderNgSuggestAdapter extends FactFinderAbstractAdapter
{
    protected const FACT_FINDER_URL_SUGGEST = 'rest/v2/suggest';

    /**
     * @param \Generated\Shared\Transfer\FactFinderNgRequestTransfer $factFinderNgRequestTransfer
     *
     * @return string
     */
    protected function getUrl(FactFinderNgRequestTransfer $factFinderNgRequestTransfer): string
    {
        return sprintf('%s/%s', static::FACT_FINDER_URL_BASE, static::FACT_FINDER_URL_SUGGEST);
    }

    /**
     * @return string
     */
    protected function getMethod(): string
    {
        return Request::METHOD_POST;
    }
}
