<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit;


use PHPUnit\Framework\TestCase;
use RstGroup\ZfGrafanaModule\Module;

class ModuleTest extends TestCase
{
    public function testItReturnsConfigurationAsArray()
    {
        // given
        $module = new Module();

        // when
        $config = $module->getConfig();

        // then
        $this->assertInternalType('array', $config);
    }
}
