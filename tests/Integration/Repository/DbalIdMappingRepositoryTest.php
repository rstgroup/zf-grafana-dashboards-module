<?php


namespace RstGroup\ZfGrafanaModule\Tests\Integration\Repository;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;
use RstGroup\ZfGrafanaModule\Repository\DbalIdMappingRepository;
use RstGroup\ZfGrafanaModule\Tests\Helpers\DashboardSimpleId;
use RstGroup\ZfGrafanaModule\Tests\Integration\TestCase;

class DbalIdMappingRepositoryTest extends TestCase
{
    const TABLE_NAME = 'id_mapping';

    /** @var DbalIdMappingRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();

        $this->repository = new DbalIdMappingRepository(
            $this->getConnection()->getConnection(),
            self::TABLE_NAME
        );
    }

    /**
     * @return string[]
     */
    public static function getTablesDefinition()
    {
        return [
            self::TABLE_NAME => file_get_contents(__DIR__ . '/../datasets/id_mapping.sql'),
        ];
    }

    public function testItSavesIdMappingToDb()
    {
        // given: local & remote IDs
        $localId = new DashboardSimpleId('abcd');
        $remoteId = new DashboardSimpleId('xyz');

        // when
        $this->repository->saveMapping($localId, $remoteId);

        // then: table contains the row with mapping
        $this->assertTableContains(
            [
                'local_id' => 'abcd',
                'remote_id' => 'xyz',
            ],
            new \PHPUnit_Extensions_Database_DataSet_QueryTable(
                self::TABLE_NAME,
                sprintf('SELECT * FROM %s', self::TABLE_NAME),
                $this->getConnection()
            )
        );
    }

    public function testItUpdatesTheExistingMapping()
    {
        // given: existing mapping
        $this->getDatabaseTester()->setDataSet(
            new \PHPUnit_Extensions_Database_DataSet_ArrayDataSet([
                self::TABLE_NAME => [
                    ['local_id' => 'abcd', 'remote_id' => 'aaa'],
                ]
            ])
        );

        // given: new mapping
        $localId = new DashboardSimpleId('abcd');
        $remoteId = new DashboardSimpleId('xyz');

        // when
        $this->repository->saveMapping($localId, $remoteId);

        // then: table contains the row with mapping
        $this->assertTableContains(
            [
                'local_id' => 'abcd',
                'remote_id' => 'xyz',
            ],
            new \PHPUnit_Extensions_Database_DataSet_QueryTable(
                self::TABLE_NAME,
                sprintf('SELECT * FROM %s', self::TABLE_NAME),
                $this->getConnection()
            )
        );
    }

    public function testItCanMapDashboardIdsIfMappingIsSavedToDb()
    {
        // given: existing mapping
        $this->getDatabaseTester()->setDataSet(
            new \PHPUnit_Extensions_Database_DataSet_ArrayDataSet([
                self::TABLE_NAME => [
                    ['local_id' => 'abcd', 'remote_id' => 'aaa'],
                ]
            ])
        );
        $this->getDatabaseTester()->onSetUp();

        // given: local id
        $localId = new DashboardSimpleId('abcd');

        // when
        $mappedId = $this->repository->mapToId($localId);

        // then
        $this->assertSame('aaa', $mappedId);
    }
}
