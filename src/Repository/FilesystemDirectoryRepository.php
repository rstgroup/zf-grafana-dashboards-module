<?php


namespace RstGroup\ZfGrafanaModule\Repository;


use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardDefinition;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardRepository;
use RstGroup\ZfGrafanaModule\Dashboard\InnerId\DashboardFilename;
use Webmozart\Assert\Assert;

final class FilesystemDirectoryRepository implements DashboardRepository
{
    use DummyDashboardMapperTrait;

    /** @var string */
    private $directory;

    /**
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    public function saveDashboard(Dashboard $dashboard)
    {
        throw new \BadMethodCallException('Not implemented! This repository is ReadOnly.');
    }

    /**
     * @param DashboardId $id ID as instance of DashboardFilename class
     * @return Dashboard
     * @throws \InvalidArgumentException
     */
    public function loadDashboard(DashboardId $id)
    {
        Assert::isInstanceOf($id, DashboardFilename::class);
        $filename = $id->getId();

        $definition = file_get_contents(
            $this->directory . DIRECTORY_SEPARATOR . $id->getId()
        );

        // check if file is loaded
        Assert::notSame(false, $definition, sprintf('Could not read from file: \'%s\'', $filename));

        return new Dashboard(
            new DashboardDefinition($definition),
            $id
        );
    }
}
