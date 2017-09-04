<?php


namespace RstGroup\ZfGrafanaModule\Dashboard;


use Webmozart\Assert\Assert;

final class DashboardMetadata
{
    private $dashboardId;
    private $grafanaId;
    private $version;
    private $schemaVersion;

    /**
     * @param DashboardId $dashboardId
     * @param int         $grafanaId
     * @param int         $version
     * @param int|null    $schemaVersion
     * @internal param DashboardId $remoteId
     */
    public function __construct(DashboardId $dashboardId, $grafanaId, $version, $schemaVersion = null)
    {
        $this->dashboardId   = $dashboardId;
        $this->grafanaId     = $grafanaId;
        $this->version       = $version;
        $this->schemaVersion = $schemaVersion;
    }

    /**
     * @return int
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
        Assert::keyExists($definition, 'id');
        Assert::integer($definition['id']);
        Assert::keyExists($definition, 'version');
        Assert::integer($definition['version']);

        if (isset($definition['schemaVersion'])) {
            Assert::integer($definition['schemaVersion']);
        }

        return new self(
            $dashboard->getId(),
            $definition['id'],
            $definition['version'],
            isset($definition['schemaVersion']) ? (int)$definition['schemaVersion'] : null
        );
    }
}
