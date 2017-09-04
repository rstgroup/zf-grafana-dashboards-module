<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit\Dashboard;


use PHPUnit\Framework\TestCase;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;

class DashboardSlugTest extends TestCase
{
    /**
     * @dataProvider alphanumericIdsProvider
     * @param string $identifier
     */
    public function testItAcceptsAlphanumericStringIdentifiers($identifier)
    {
        // when
        $dashboardId = new DashboardSlug($identifier);

        // then
        $this->assertSame($identifier, $dashboardId->getId());
    }

    public function alphanumericIdsProvider()
    {
        return [
            ['abcd'],
            ['1234'],
            ['abc-123'],
        ];
    }

    public function testItThrowsExceptionOnEmptyString()
    {
        // expect
        $this->expectException(\InvalidArgumentException::class);

        // when
        new DashboardSlug('');
    }

    public function testItMapsItselfToString()
    {
        // when
        $dashboardId = new DashboardSlug("some-id");

        // then
        $this->assertSame('some-id', (string)$dashboardId);
    }
}
