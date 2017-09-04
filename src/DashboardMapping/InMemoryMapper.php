<?php


namespace RstGroup\ZfGrafanaModule\DashboardMapping;


use RstGroup\ZfGrafanaModule\Dashboard\DashboardId;

/**
 * This mapper can be used for manual mapping (if dashboards already exists in Grafana).
 */
final class InMemoryMapper implements DashboardIdRepoToRepoMapper
{
    /** @var DashboardId[] */
    private $idToIdMapping;

    /**
     * @param DashboardId[] $idToIdMapping
     */
    public function __construct(array $idToIdMapping)
    {
        $this->idToIdMapping        = $idToIdMapping;
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
     * @param array       $mapping
     * @param DashboardId $id
     * @return mixed
     */
    private function findMapping(array &$mapping, DashboardId $id)
    {
        return isset($mapping[$id->getId()]) ? $mapping[$id->getId()] : null;
    }
}
