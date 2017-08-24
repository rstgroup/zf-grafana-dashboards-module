<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit\Dashboard;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardDefinition;
use PHPUnit\Framework\TestCase;

class DashboardDefinitionTest extends TestCase
{
    public function testItAcceptsValidJsons()
    {
        // when
        $definition = new DashboardDefinition("{}");

        // then
        $this->assertSame('{}', $definition->getDefinition());
    }

    public function testIfThrowsExceptionOnInvalidJson()
    {
        // expect
        $this->expectException(\InvalidArgumentException::class);

        // when
        new DashboardDefinition('{"abc":2');
    }
}
