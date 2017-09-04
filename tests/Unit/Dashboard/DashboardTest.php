<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit\Dashboard;


use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use PHPUnit\Framework\TestCase;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardDefinition;

class DashboardTest extends TestCase
{
    public function testItTellsItsNotEqualToAnotherDashboardByDefinition()
    {
        // given: two instances
        $dashA = new Dashboard(new DashboardDefinition('{"title":"Dash A"}'));
        $dashB = new Dashboard(new DashboardDefinition('{"title":"Dash B"}'));

        // when
        $isEqual = $dashA->isEqual($dashB);

        // then
        $this->assertFalse($isEqual);
    }

    public function testItTellsItsEqualWhenOnlyMetadataIsDifferent()
    {
        // given: two instances
        $dashA = new Dashboard(new DashboardDefinition('{"title":"Dash A","version":1}'));
        $dashB = new Dashboard(new DashboardDefinition('{"title":"Dash A","version":2}'));

        // when
        $isEqual = $dashA->isEqual($dashB);

        // then
        $this->assertTrue($isEqual);
    }


}
