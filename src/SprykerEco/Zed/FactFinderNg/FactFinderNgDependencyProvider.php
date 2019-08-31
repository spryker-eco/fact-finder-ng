<?php

namespace SprykerEco\Zed\FactFinderNg;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class FactFinderNgDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_FACT_FINDER_NG = 'CLIENT_FACT_FINDER_NG';

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = $this->addFactFinderNgClient($container);

        return parent::provideCommunicationLayerDependencies($container);
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    protected function addFactFinderNgClient(Container $container): Container
    {
        $container->set(static::CLIENT_FACT_FINDER_NG, function (Container $container) {
            return $container->getLocator()->factFinderNg()->client();
        });

        return $container;
    }
}
