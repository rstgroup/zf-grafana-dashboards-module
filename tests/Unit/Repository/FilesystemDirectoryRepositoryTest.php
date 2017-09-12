<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit\Repository;


use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardDefinition;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;
use RstGroup\ZfGrafanaModule\Dashboard\InnerId\DashboardFilename;
use RstGroup\ZfGrafanaModule\Repository\FilesystemDirectoryRepository;
use PHPUnit\Framework\TestCase;

class FilesystemDirectoryRepositoryTest extends TestCase
{
    public function testThisRepositoryIsReadOnly()
    {
        // given: repo
        $repository = new FilesystemDirectoryRepository('/');
        $dashboard = new Dashboard(new DashboardDefinition('{}'), new DashboardSlug('a'));

        // expect
        $this->expectException(\BadMethodCallException::class);

        // when
        $repository->saveDashboard($dashboard);
    }

    public function testRepositoryCanReadDashboardFromFile()
    {
        // given: virtual filesystem with dashboard file
        $root = vfsStream::setup('directory', null, [
            'test-dashboard.json' => '{"title":"Test dashboard"}',
        ]);
        $dashboardId = new DashboardFilename('test-dashboard.json');

        // given: repository itself
        $repository = new FilesystemDirectoryRepository(
            $root->url()
        );

        // when
        $dashboard = $repository->loadDashboard($dashboardId);

        // then
        $this->assertInstanceOf(Dashboard::class, $dashboard);
        $this->assertSame('{"title":"Test dashboard"}', $dashboard->getDefinition()->getDefinition());
        $this->assertSame($dashboardId, $dashboard->getId());
    }
}
