<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit\Controller;


use RstGroup\ZfGrafanaModule\Controller\DashboardMigrationsController;
use PHPUnit\Framework\TestCase;
use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardDefinition;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadata;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardRepository;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;
use RstGroup\ZfGrafanaModule\Dashboard\InnerId\DashboardFilename;
use RstGroup\ZfGrafanaModule\DashboardMapping\DashboardToDashboardMapper;
use RstGroup\ZfGrafanaModule\Repository\Mapper\DummyDashboardMapper;
use RstGroup\ZfGrafanaModule\Tests\Helpers\InMemoryMappingRepository;
use RstGroup\ZfGrafanaModule\Tests\Helpers\InMemoryMetadataRepository;

class DashboardMigrationsControllerTest extends TestCase
{
    public function testItPerformsMigrationWhenRemoteRepositoryDoesNotHaveTheDashboard()
    {
        // given: dashboard to test
        $dashboard = new Dashboard(
            DashboardDefinition::createFromArray([
                'version'       => 1,
                'schemaVersion' => 5,
                'rows'          => [
                    [
                        'height' => '200px',
                    ],
                ],
                'timezone'      => 'utc',
            ]),
            new DashboardFilename('dashboard.json')
        );

        // given: dashboard processed by Grafana API
        $newDashboardDefinition = DashboardDefinition::createFromArray(array_replace(
            $dashboard->getDefinition()->getDecodedDefinition(),
            ['id' => 123, 'version' => 1, 'schemaVersion' => 5]
        ));
        $dashboardSlug          = new DashboardSlug('my-dashboard');
        $savedDashboard         = new Dashboard($newDashboardDefinition, $dashboardSlug);

        // given: some mocks for testing
        $targetRepository = $this->getMockBuilder(DashboardRepository::class)->getMock();
        $sourceRepository = $this->getMockBuilder(DashboardRepository::class)->getMock();

        $metadataRepository = new InMemoryMetadataRepository();
        $mappingRepository  = new InMemoryMappingRepository();

        // given: mock the target repository
        $targetRepository->method('getDashboardMapper')->willReturn(
            new DummyDashboardMapper()
        );

        // given: controller
        $controller = new DashboardMigrationsController(
            [$dashboard->getId()],
            $sourceRepository,
            $targetRepository,
            $metadataRepository,
            $mappingRepository
        );

        // expect: controller will fetch dashboard by its ID
        $sourceRepository->expects($this->once())->method('loadDashboard')->with($dashboard->getId())->willReturn($dashboard);

        // expect: controller will try to push dashboard straight to remote repository
        $targetRepository->expects($this->once())->method('saveDashboard')->willReturn($savedDashboard);

        // when
        $controller->migrateAction();

        // then: metadata is stored with proper values
        /** @var DashboardMetadata $storedMetadata */
        $storedMetadata = $metadataRepository->fetchMetadata($dashboardSlug);

        $this->assertInstanceOf(DashboardMetadata::class, $storedMetadata);
        $this->assertSame($dashboardSlug->getId(), $storedMetadata->getDashboardId()->getId());
        $this->assertSame(123, $storedMetadata->getGrafanaId());
        $this->assertSame(1, $storedMetadata->getVersion());
        $this->assertSame(5, $storedMetadata->getSchemaVersion());

        // then: id mapping is stored
        $this->assertSame($dashboardSlug->getId(), $mappingRepository->mapToId($dashboard->getId())->getId());
    }

    public function testItPerformsMigrationWhenRemoteRepositoryDoesHaveTheDashboardWithDifferentDefinition()
    {
        // given: dashboard to test
        $dashboard = new Dashboard(
            DashboardDefinition::createFromArray([
                'title'         => 'Remote dashboard',
                'version'       => 1,
                'schemaVersion' => 5,
                'rows'          => [
                    [
                        'height' => '200px',
                    ],
                ],
                'timezone'      => 'utc',
            ]),
            new DashboardFilename('dashboard.json')
        );

        // given: dashboard stored in remote repository
        $remoteDashboard = new Dashboard(
            DashboardDefinition::createFromArray([
                'id'            => 345,
                'title'         => 'Remote dashboard',
                'version'       => 3,
                'schemaVersion' => 5,
                'rows'          => [
                    [
                        'height' => '100px',
                    ],
                ],
                'timezone'      => 'utc',
            ]),
            new DashboardSlug('remote-dashboard')
        );

        $savedDashboard = new Dashboard(
            DashboardDefinition::createFromArray([
                'id'            => 345,
                'title'         => 'Remote dashboard',
                'version'       => 4,
                'schemaVersion' => 5,
                'rows'          => [
                    [
                        'height' => '200px',
                    ],
                ],
                'timezone'      => 'utc',
            ]),
            new DashboardSlug('remote-dashboard')
        );

        // given: some mocks for testing
        $targetRepository = $this->getMockBuilder(DashboardRepository::class)->getMock();
        $targetRepositoryMapper = $this->getMockBuilder(DashboardToDashboardMapper::class)->getMock();
        $sourceRepository = $this->getMockBuilder(DashboardRepository::class)->getMock();

        $metadataRepository = new InMemoryMetadataRepository();
        $mappingRepository  = new InMemoryMappingRepository([
            'dashboard.json' => 'remote-dashboard',
        ]);

        // given: controller
        $controller = new DashboardMigrationsController(
            [$dashboard->getId()],
            $sourceRepository,
            $targetRepository,
            $metadataRepository,
            $mappingRepository
        );

        // given: mock the target repository
        $targetRepository->expects($this->any())->method('getDashboardMapper')->willReturn($targetRepositoryMapper);

        // expect: controller will fetch dashboard by its ID
        $sourceRepository->expects($this->once())->method('loadDashboard')->with($dashboard->getId())->willReturn($dashboard);

        // expect: target repository's mapper will map local dashboard to remote format
        $targetRepositoryMapper->expects($this->atLeastOnce())->method('map')->with($dashboard)->willReturn($dashboard);

        // expect: controller will fetch remote dashboard
        $targetRepository->expects($this->once())->method('loadDashboard')->willReturn($remoteDashboard);


        // expect: controller will try to push dashboard straight to remote repository
        $targetRepository->expects($this->once())->method('saveDashboard')
            ->with($dashboard)
            ->willReturn($savedDashboard);

        // when
        $controller->migrateAction();

        // then: metadata is stored with proper values
        /** @var DashboardMetadata $storedMetadata */
        $storedMetadata = $metadataRepository->fetchMetadata($remoteDashboard->getId());

        $this->assertInstanceOf(DashboardMetadata::class, $storedMetadata);
        $this->assertSame($remoteDashboard->getId()->getId(), $storedMetadata->getDashboardId()->getId());
        $this->assertSame(345, $storedMetadata->getGrafanaId());
        $this->assertSame(4, $storedMetadata->getVersion());
        $this->assertSame(5, $storedMetadata->getSchemaVersion());

        // then: id mapping is stored
        $this->assertSame($remoteDashboard->getId()->getId(), $mappingRepository->mapToId($dashboard->getId())->getId());
    }

    public function testItDoesNotPerformMigrationWhenRemoteRepositoryHaveTheDashboardWithSameDefinition()
    {
        $this->markTestSkipped("Not ready yet.");

        // given: dashboard to test
        $dashboard = new Dashboard(
            DashboardDefinition::createFromArray([
                'title'         => 'Remote dashboard',
                'version'       => 1,
                'schemaVersion' => 5,
                'rows'          => [
                    [
                        'height' => '200px',
                    ],
                ],
                'timezone'      => 'utc',
            ]),
            new DashboardFilename('dashboard.json')
        );

        // given: dashboard stored in remote repository
        $remoteDashboard = new Dashboard(
            DashboardDefinition::createFromArray([
                'title'         => 'Remote dashboard',
                'version'       => 1,
                'schemaVersion' => 5,
                'rows'          => [
                    [
                        'height' => '200px',
                    ],
                ],
                'timezone'      => 'utc',
            ]),
            new DashboardSlug('remote-dashboard')
        );

        $savedDashboard = new Dashboard(
            DashboardDefinition::createFromArray([
                'id'            => 345,
                'title'         => 'Remote dashboard',
                'version'       => 4,
                'schemaVersion' => 5,
                'rows'          => [
                    [
                        'height' => '200px',
                    ],
                ],
                'timezone'      => 'utc',
            ]),
            new DashboardSlug('remote-dashboard')
        );

        // given: some mocks for testing
        $targetRepository = $this->getMockBuilder(DashboardRepository::class)->getMock();
        $targetRepositoryMapper = $this->getMockBuilder(DashboardToDashboardMapper::class)->getMock();
        $sourceRepository = $this->getMockBuilder(DashboardRepository::class)->getMock();

        $metadataRepository = new InMemoryMetadataRepository();
        $mappingRepository  = new InMemoryMappingRepository([
            'dashboard.json' => 'remote-dashboard',
        ]);

        // given: controller
        $controller = new DashboardMigrationsController(
            [$dashboard->getId()],
            $sourceRepository,
            $targetRepository,
            $metadataRepository,
            $mappingRepository
        );

        // given: mock the target repository
        $targetRepository->expects($this->any())->method('getDashboardMapper')->willReturn($targetRepositoryMapper);

        // expect: controller will fetch dashboard by its ID
        $sourceRepository->expects($this->once())->method('loadDashboard')->with($dashboard->getId())->willReturn($dashboard);

        // expect: target repository's mapper will map local dashboard to remote format
        $targetRepositoryMapper->expects($this->atLeastOnce())->method('map')->with($dashboard)->willReturn($dashboard);

        // expect: controller will fetch remote dashboard
        $targetRepository->expects($this->once())->method('loadDashboard')->willReturn($remoteDashboard);


        // expect: controller will try to push dashboard straight to remote repository
        $targetRepository->expects($this->once())->method('saveDashboard')
            ->with($dashboard)
            ->willReturn($savedDashboard);

        // when
        $controller->migrateAction();

        // then: metadata is stored with proper values
        /** @var DashboardMetadata $storedMetadata */
        $storedMetadata = $metadataRepository->fetchMetadata($remoteDashboard->getId());

        $this->assertInstanceOf(DashboardMetadata::class, $storedMetadata);
        $this->assertSame($remoteDashboard->getId()->getId(), $storedMetadata->getDashboardId()->getId());
        $this->assertSame(345, $storedMetadata->getGrafanaId());
        $this->assertSame(4, $storedMetadata->getVersion());
        $this->assertSame(5, $storedMetadata->getSchemaVersion());

        // then: id mapping is stored
        $this->assertSame($remoteDashboard->getId()->getId(), $mappingRepository->mapToId($dashboard->getId())->getId());
    }
}
