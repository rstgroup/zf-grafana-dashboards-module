<?php


namespace RstGroup\ZfGrafanaModule\Tests\Unit\Dashboard\InnerId;


use RstGroup\ZfGrafanaModule\Dashboard\InnerId\DashboardFilename;
use PHPUnit\Framework\TestCase;

class DashboardFilenameTest extends TestCase
{
    public function testItRefusesFilesWithExtensionDifferentThanJson()
    {
        // given: invalid filename
        $filename = 'dashboard.txt';

        // expect
        $this->expectException(\InvalidArgumentException::class);

        // when
        new DashboardFilename($filename);
    }

    /**
     * @dataProvider pathProvider
     *
     * @param string $path
     * @param string $expectedFilename
     */
    public function testItCanBeCreatedFromPath($path, $expectedFilename)
    {
        // when
        $id = DashboardFilename::createFromPath($path);

        // then
        $this->assertSame($expectedFilename, $id->getId());
    }

    public function pathProvider()
    {
        return [
            'relative path' => ['../other-dir/file.json', 'file.json'],
            'relative path 2' => ['sub/dashboard.json', 'dashboard.json'],
            'absolute path' => ['/home/user/dashboard.json', 'dashboard.json'],
        ];
    }
}
