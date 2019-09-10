<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\FactFinderNg\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerEco\Client\FactFinderNg\FactFinderNgClientInterface;
use SprykerEco\Zed\FactFinderNg\FactFinderNgDependencyProvider;

class FactFinderNgCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \SprykerEco\Client\FactFinderNg\FactFinderNgClientInterface
     */
    public function getFactFinderNgClient(): FactFinderNgClientInterface
    {
        return $this->getProvidedDependency(FactFinderNgDependencyProvider::CLIENT_FACT_FINDER_NG);
    }
}
