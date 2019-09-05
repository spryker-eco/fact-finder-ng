<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\FactFinderNg\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \SprykerEco\Zed\FactFinderNg\Communication\FactFinderNgCommunicationFactory getFactory()
 */
class FactFinderNgImportSearchConsole extends Console
{
    public const COMMAND_NAME = 'fact-finder-ng:import:search';
    public const DESCRIPTION = 'Trigger importing of search data. Url for file is defined on FF side.';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription(self::DESCRIPTION);

        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $responseTransfer = $this->getFactory()->getFactFinderNgClient()->triggerSearchImport();

        return $responseTransfer->getIsSuccess() ? static::CODE_SUCCESS : static::CODE_ERROR;
    }
}
