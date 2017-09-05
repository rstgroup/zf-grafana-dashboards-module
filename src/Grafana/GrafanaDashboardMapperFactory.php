<?php


namespace RstGroup\ZfGrafanaModule\Grafana;


use Psr\Container\ContainerInterface;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadataRepository;
use RstGroup\ZfGrafanaModule\DashboardMapping\DashboardIdRepoToRepoMapper;

final class GrafanaDashboardMapperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $dashboardIdToIdMapper = $container->get(DashboardIdRepoToRepoMapper::class);
        $metadataRepository    = $container->get(DashboardMetadataRepository::class);

        return new GrafanaDashboardMapper(
            $dashboardIdToIdMapper,
            $metadataRepository
        );
    }
}
