<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Client\FactFinderNg;

use Spryker\Client\Catalog\Plugin\Elasticsearch\Query\CatalogSearchQueryPlugin;

/**
 * @group SprykerEcoTest
 * @group Client
 * @group FactFinderNg
 * @group FactFinderNgTest
 * @group FactFinderNgClientTest
 */
class FactFinderNgClientTest extends AbstractFactFinderNgClientTest
{
    /**
     * @return void
     */
    public function testSearch(): void
    {
        //Arrange
        $client = $this->prepareClient();

        //Act
        $result = $client->search(new CatalogSearchQueryPlugin(), [], []);

        //Assert
        $this->assertIsArray($result);
        $this->assertEquals($this->getSearchResult(), $result);
    }

    /**
     * @return void
     */
    public function testSuggest(): void
    {
        //Arrange
        $client = $this->prepareClient();

        //Act
        $result = $client->suggest(new CatalogSearchQueryPlugin(), [], []);

        //Assert
        $this->assertIsArray($result);
        $this->assertEquals($this->getSearchResult(), $result);
    }

    /**
     * @return void
     */
    public function testNavigation(): void
    {
        //Arrange
        $client = $this->prepareClient();

        //Act
        $result = $client->navigation(new CatalogSearchQueryPlugin(), [], []);

        //Assert
        $this->assertIsArray($result);
        $this->assertEquals($this->getSearchResult(), $result);
    }

    /**
     * @return void
     */
    public function testTrackCheckoutEvent(): void
    {
        //Arrange
        $client = $this->prepareClient();

        //Act
        $response = $client->trackCheckoutEvent($this->prepareCartOrCheckoutEventTransfers());

        //Assert
        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testTrackCartEvent(): void
    {
        //Arrange
        $client = $this->prepareClient();

        //Act
        $response = $client->trackCartEvent($this->prepareCartOrCheckoutEventTransfers());

        //Assert
        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testTrackClickEvent(): void
    {
        //Arrange
        $client = $this->prepareClient();

        //Act
        $response = $client->trackCartEvent($this->prepareClickEventTransfers());

        //Assert
        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testTriggerSearchImport(): void
    {
        //Arrange
        $client = $this->prepareClient();

        //Act
        $response = $client->triggerSearchImport();

        //Assert
        $this->assertTrue($response->getIsSuccess());
    }
}
