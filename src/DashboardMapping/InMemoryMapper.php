<?php


namespace RstGroup\ZfGrafanaModule\DashboardMapping;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;

/**
 * This mapper can be used for manual mapping (if dashboards already exists in Grafana).
 */
final class InMemoryMapper implements DashboardIdRepoToRepoMapper, DashboardIdToGrafanaIdMapper
{
    /** @var DashboardId[] */
    private $idToIdMapping;
    /** @var int[] */
    private $idToGrafanaIdMapping;

    /**
     * @param DashboardId[] $idToIdMapping
     * @param \int[]        $idToGrafanaIdMapping
     */
    public function __construct(array $idToIdMapping, array $idToGrafanaIdMapping)
    {
        $this->idToIdMapping        = $idToIdMapping;
        $this->idToGrafanaIdMapping = $idToGrafanaIdMapping;
    }

    /**
     * @param DashboardId $id
     * @return DashboardId|null
     */
    public function mapToId(DashboardId $id)
    {
        return $this->findMapping($this->idToIdMapping, $id);
    }

    /**
     * @param DashboardId $id
     * @return int|null
     */
    public function mapToGrafanaId(DashboardId $id)
    {
        return $this->findMapping($this->idToGrafanaIdMapping, $id);
    }

    /**
     * @param array       $mapping
     * @param DashboardId $id
     * @return mixed
     */
    private function findMapping(array &$mapping, DashboardId $id)
    {
        return isset($mapping[$id->getId()]) ? $mapping[$id->getId()] : null;
    }
}
