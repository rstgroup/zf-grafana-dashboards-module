<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit\Dashboard;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardDefinition;
use PHPUnit\Framework\TestCase;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadata;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;

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

    public function testItCanApplyMetadataToDefinitionThusCreatingNewDefinition()
    {
        // given: source definition
        $definition = new DashboardDefinition('{"id":null,"title":"abcd"}');

        // given: metadata to apply:
        $metadata = new DashboardMetadata(new DashboardSlug('xxx'), 2, 1, 3);

        // when
        $newDefinition = $definition->withMetadata($metadata);

        // then
        $this->assertArraySubset([
            'id'            => 1,
            'version'       => 2,
            'schemaVersion' => 3,
        ], $newDefinition->getDecodedDefinition(), true);
    }
}
