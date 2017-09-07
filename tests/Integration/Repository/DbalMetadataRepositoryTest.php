<?php


namespace RstGroup\ZfGrafanaModule\Tests\Integration\Repository;

use PHPUnit_Extensions_Database_DataSet_IDataSet;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadata;
use RstGroup\ZfGrafanaModule\Repository\DbalMetadataRepository;
use RstGroup\ZfGrafanaModule\Tests\Helpers\DashboardSimpleId;
use RstGroup\ZfGrafanaModule\Tests\Integration\TestCase;

class DbalMetadataRepositoryTest extends TestCase
{
    const TABLE_NAME = 'dashboard_metadata';

    /** @var  DbalMetadataRepository */
    private $repository;

    public function setUp()
    {
        $this->repository = new DbalMetadataRepository(
            $this->getConnection()->getConnection(),
            self::TABLE_NAME
        );

        parent::setUp();
    }

    /**
     * @return string[]
     */
    public static function getTablesDefinition()
    {
        return [
            self::TABLE_NAME => file_get_contents(__DIR__ . '/../datasets/dashboard_metadata.sql', FILE_TEXT),
        ];
    }

    public function testItSavesMetadataForTheFirstTime()
    {
        // given:
        $metadata = new DashboardMetadata(
            new DashboardSimpleId('dash-id'), 2, 3, 4
        );

        // when
        $this->repository->saveMetadata($metadata);

        // then: table contains the row
        $this->assertTableContains(
            [
                'dashboard_id' => 'dash-id',
                'dashboard_version' => 2,
                'grafana_id' => 3,
                'dashboard_schema_version' => 4,
            ],
            new \PHPUnit_Extensions_Database_DataSet_QueryTable(
                self::TABLE_NAME,
                sprintf('SELECT * FROM %s', self::TABLE_NAME),
                $this->getConnection()
            )
        );
    }

    public function testItUpdatesStoredMetadata()
    {
        // given: setup fixture
        $this->getDatabaseTester()->setDataSet(
            new \PHPUnit_Extensions_Database_DataSet_ArrayDataSet([
                self::TABLE_NAME => [
                    [
                        'dashboard_id' => 'dash-id',
                        'grafana_id' => 999,
                        'dashboard_version' => 999,
                        'dashboard_schema_version' => 999,
                    ]
                ]
            ])
        );
        $this->getDatabaseTester()->onSetUp();

        // given: metadata
        $metadata = new DashboardMetadata(
            new DashboardSimpleId('dash-id'), 2, 3, 4
        );

        // when
        $this->repository->saveMetadata($metadata);

        // then: table contains the row
        $this->assertTableContains(
            [
                'dashboard_id' => 'dash-id',
                'dashboard_version' => 2,
                'grafana_id' => 3,
                'dashboard_schema_version' => 4,
            ],
            new \PHPUnit_Extensions_Database_DataSet_QueryTable(
                self::TABLE_NAME,
                sprintf('SELECT * FROM %s', self::TABLE_NAME),
                $this->getConnection()
            )
        );
    }

    public function testIfFetchesMetadataFromDb()
    {
        // given: setup fixture
        $this->getDatabaseTester()->setDataSet(
            new \PHPUnit_Extensions_Database_DataSet_ArrayDataSet([
                self::TABLE_NAME => [
                    [
                        'dashboard_id' => 'dash-id',
                        'grafana_id' => 999,
                        'dashboard_version' => 888,
                        'dashboard_schema_version' => 777,
                    ]
                ]
            ])
        );
        $this->getDatabaseTester()->onSetUp();

        // when
        $metadata = $this->repository->fetchMetadata(new DashboardSimpleId('dash-id'));

        // then: it's metadata instance
        $this->assertInstanceOf(DashboardMetadata::class, $metadata);

        // then: params do match
        $this->assertSame('dash-id', $metadata->getDashboardId()->getId());
        $this->assertSame(999, $metadata->getGrafanaId());
        $this->assertSame(888, $metadata->getVersion());
        $this->assertSame(777, $metadata->getSchemaVersion());
    }
}
