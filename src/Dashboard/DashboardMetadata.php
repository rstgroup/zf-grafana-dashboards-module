<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;


use Webmozart\Assert\Assert;

final class DashboardMetadata
{
    /** @var DashboardId */
    private $dashboardId;
    /** @var int */
    private $version;
    /** @var int|null */
    private $grafanaId;
    /** @var int|null */
    private $schemaVersion;

    /**
     * @param DashboardId $dashboardId
     * @param int         $version
     * @param int|null    $grafanaId
     * @param int|null    $schemaVersion
     */
    public function __construct(DashboardId $dashboardId, $version, $grafanaId = null, $schemaVersion = null)
    {
        $this->dashboardId   = $dashboardId;
        $this->grafanaId     = $grafanaId;
        $this->version       = $version;
        $this->schemaVersion = $schemaVersion;
    }

    /**
     * @return int|null
     */
    public function getGrafanaId()
    {
        return $this->grafanaId;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return DashboardId
     */
    public function getDashboardId()
    {
        return $this->dashboardId;
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return $this->schemaVersion;
    }

    /**
     * @param Dashboard $dashboard
     * @return DashboardMetadata
     * @throws \InvalidArgumentException
     */
    public static function createFromDashboard(Dashboard $dashboard)
    {
        $definition = $dashboard->getDefinition()->getDecodedDefinition();

        Assert::isArray($definition);
        Assert::keyExists($definition, 'version');
        Assert::integer($definition['version']);

        if (isset($definition['schemaVersion'])) {
            Assert::integer($definition['schemaVersion']);
        }

        return new self(
            $dashboard->getId(),
            $definition['version'],
            isset($definition['id']) ? (int)$definition['id'] : null,
            isset($definition['schemaVersion']) ? (int)$definition['schemaVersion'] : null
        );
    }
}
