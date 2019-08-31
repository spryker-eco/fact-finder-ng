<?php

namespace SprykerEco\Zed\FactFinderNg\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerEco\Client\FactFinderNg\FactFinderNgClientInterface;
use SprykerEco\Zed\FactFinderNg\FactFinderNgDependencyProvider;

class FactFinderNgCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return FactFinderNgClientInterface
     */
    public function getFactFinderNgClient(): FactFinderNgClientInterface
    {
        return $this->getProvidedDependency(FactFinderNgDependencyProvider::CLIENT_FACT_FINDER_NG);
    }
}
