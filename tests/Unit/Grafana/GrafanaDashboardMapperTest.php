<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit\Grafana;


use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardDefinition;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadata;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;
use RstGroup\ZfGrafanaModule\Grafana\GrafanaDashboardMapper;
use PHPUnit\Framework\TestCase;
use RstGroup\ZfGrafanaModule\Tests\Helpers\DashboardSimpleId;
use RstGroup\ZfGrafanaModule\Tests\Helpers\InMemoryMappingRepository;
use RstGroup\ZfGrafanaModule\Tests\Helpers\InMemoryMetadataRepository;

class GrafanaDashboardMapperTest extends TestCase
{
    public function testItMapsDashboardWithSlugFromMapper()
    {
        // given: known dashboard ID
        $dashboard = new Dashboard(
            DashboardDefinition::createFromArray([]),
            new DashboardSimpleId('abcd')
        );

        // given: mocked requirements
        $idMapper           = new InMemoryMappingRepository(['abcd' => 'xyz']);
        $metadataRepository = new InMemoryMetadataRepository();

        // given: mapper to test
        $mapper = new GrafanaDashboardMapper(
            $idMapper,
            $metadataRepository
        );

        // when
        $mappedDashboard = $mapper->map($dashboard);

        // then: ID has been mapped
        $this->assertInstanceOf(Dashboard::class, $mappedDashboard);
        $this->assertInstanceOf(DashboardSlug::class, $mappedDashboard->getId());
        $this->assertSame('xyz', $mappedDashboard->getId()->getId());
    }

    public function testItMapsDashboardWithoutIdIfIdMappingIsNotPresent()
    {
        // given: unknown dashboard ID
        $dashboard = new Dashboard(
            DashboardDefinition::createFromArray([]),
            new DashboardSimpleId('abcd')
        );

        // given: mapper to test
        $mapper = new GrafanaDashboardMapper(
            new InMemoryMappingRepository(),
            new InMemoryMetadataRepository()
        );

        // when
        $mappedDashboard = $mapper->map($dashboard);

        // then: ID has been mapped
        $this->assertInstanceOf(Dashboard::class, $mappedDashboard);
        $this->assertNull($mappedDashboard->getId());
    }

    public function testItMapsDashboardByApplyingStoredMetadata()
    {
        // given: dashboard to map
        $dashboard = new Dashboard(
            DashboardDefinition::createFromArray([
                'id'   => null,
                'rows' => [],
            ]),
            new DashboardSimpleId('abcd')
        );

        // given: dashboard ID of remote dashboard
        $remoteDashboardId = new DashboardSlug('xyz');

        // given: stored metadata
        $dashboardsMetadata = new DashboardMetadata(
            $remoteDashboardId, 22, 555, 1
        );

        // given: mocked requirements
        $idMapper           = new InMemoryMappingRepository([
            $dashboard->getId()->getId() => $remoteDashboardId->getId(),
        ]);
        $metadataRepository = new InMemoryMetadataRepository([
            $remoteDashboardId->getId() => $dashboardsMetadata,
        ]);

        // given: mapper to test
        $mapper = new GrafanaDashboardMapper(
            $idMapper,
            $metadataRepository
        );

        // when
        $mappedDashboard = $mapper->map($dashboard);

        // then: metadata has been applied
        $this->assertInstanceOf(Dashboard::class, $mappedDashboard);
        $this->assertEquals([
            'id'            => 555,
            'rows'          => [],
            'version'       => 22,
            'schemaVersion' => 1,
        ], $mappedDashboard->getDefinition()->getDecodedDefinition());
    }
}
