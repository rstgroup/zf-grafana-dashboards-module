<?php


namespace RstGroup\ZfGrafanaModule\Grafana;


use RstGroup\ZfGrafanaModule\Dashboard\Dashboard;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadataRepository;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardSlug;
use RstGroup\ZfGrafanaModule\DashboardMapping\DashboardIdRepoToRepoMapper;
use RstGroup\ZfGrafanaModule\DashboardMapping\DashboardToDashboardMapper;

final class GrafanaDashboardMapper implements DashboardToDashboardMapper
{
    /** @var DashboardIdRepoToRepoMapper */
    private $dashboardIdToIdMapper;

    /** @var DashboardMetadataRepository */
    private $metadataRepository;

    /**
     * @param DashboardIdRepoToRepoMapper $dashboardIdToIdMapper
     * @param DashboardMetadataRepository $metadataRepository
     */
    public function __construct(DashboardIdRepoToRepoMapper $dashboardIdToIdMapper, DashboardMetadataRepository $metadataRepository)
    {
        $this->dashboardIdToIdMapper = $dashboardIdToIdMapper;
        $this->metadataRepository    = $metadataRepository;
    }

    /**
     * @param Dashboard $dashboard
     * @return Dashboard
     */
    public function map(Dashboard $dashboard)
    {
        // map local ID to remote ID
        $mappedId = $this->dashboardIdToIdMapper->mapToId($dashboard->getId());
        $definition = $dashboard->getDefinition();

        if ($mappedId !== null) {
            // pass class name
            $mappedId = new DashboardSlug($mappedId);
            $metadata = $this->metadataRepository->fetchMetadata($mappedId);

            if ($metadata !== null) {
                $definition = $definition->withMetadata($metadata);
            }
        }

        // create new Dashboard
        return new Dashboard(
            $definition,
            $mappedId
        );
    }
}
