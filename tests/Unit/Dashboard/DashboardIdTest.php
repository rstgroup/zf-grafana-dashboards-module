<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit\Dashboard;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;
use PHPUnit\Framework\TestCase;

class DashboardIdTest extends TestCase
{
    public function testItAcceptsPositiveIntegers()
    {
        // when
        $id = new DashboardId(5);

        // then
        $this->assertSame(5, $id->getId());
    }

    public function testItThrowsExceptionWhenZeroGiven()
    {
        // expect
        $this->expectException(\InvalidArgumentException::class);

        // when
        new DashboardId(0);
    }

    public function testItThrowsExceptionWhenNegativeIntegerGiven()
    {
        // expect
        $this->expectException(\InvalidArgumentException::class);

        // when
        new DashboardId(-2);
    }
}
