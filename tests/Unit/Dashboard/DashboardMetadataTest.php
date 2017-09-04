<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit\Dashboard;


use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardDefinition;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadata;
use PHPUnit\Framework\TestCase;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;

class DashboardMetadataTest extends TestCase
{
    public function testItCanBeCreatedStraightFromDashboard()
    {
        // given: source dashboard
        $dashboard = new Dashboard(DashboardDefinition::createFromArray([
            'id'            => 123,
            'version'       => 1,
            'schemaVersion' => 7,
        ]), new DashboardSlug('abcd'));

        // when
        $metadata = DashboardMetadata::createFromDashboard($dashboard);

        // then
        $this->assertSame(123, $metadata->getGrafanaId());
        $this->assertSame(1, $metadata->getVersion());
        $this->assertSame(7,  $metadata->getSchemaVersion());
    }
}
