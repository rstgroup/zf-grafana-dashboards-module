<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit\DashboardMapping;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;
use RstGroup\ZfGrafanaModule\DashboardMapping\InMemoryMapper;
use PHPUnit\Framework\TestCase;

class InMemoryMapperTest extends TestCase
{
    public function testItCanMapDashboardIdToDashboardId()
    {
        $mappedFrom = new DashboardSlug('abc');
        $mappedTo = new DashboardSlug('xyz');

        // given: initialized mapper
        $mapper = new InMemoryMapper([
            $mappedFrom->getId() => $mappedTo,
        ], []);

        // when
        $mapped = $mapper->mapToId($mappedFrom);

        // then
        $this->assertSame($mappedTo, $mapped);
    }
}
