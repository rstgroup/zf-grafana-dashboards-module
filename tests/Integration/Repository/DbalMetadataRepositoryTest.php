<?php


namespace RstGroup\ZfGrafanaModule\Tests\Integration\Repository;

use PHPUnit_Extensions_Database_DataSet_IDataSet;
use RstGroup\ZfGrafanaModule\Tests\Integration\TestCase;

class DbalMetadataRepositoryTest extends TestCase
{
    private static $table = 'dashboard_metadata';

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return;
    }

    public static function setUpBeforeClass()
    {

    }

    public static function tearDownAfterClass()
    {

    }

    public function setUp()
    {

    }

    public function testItSavesMetadataForTheFirstTime()
    {
        // given: empty
    }
}
