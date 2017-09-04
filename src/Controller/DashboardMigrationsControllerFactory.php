<?php


namespace RstGroup\ZfGrafanaModule\Controller;

use Psr\Container\ContainerInterface;
use RstGroup\ZfGrafanaModule\Controller\Helper\DashboardIdsProvider;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadataRepository;
use RstGroup\ZfGrafanaModule\DashboardMapping\DashboardIdMappingRepository;

/** @codeCoverageIgnore */
final class DashboardMigrationsControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var array $config */
        $config = $container->get('config');

        /** @var DashboardIdsProvider $idsProvider */
        $idsProvider = $container->get(DashboardIdsProvider::class);

        return new DashboardMigrationsController(
            $idsProvider->getDashboardIds(),
            $container->get($config['dashboard-migrations']['source-repository']['service']),
            $container->get($config['dashboard-migrations']['target-repository']['service']),
            $container->get(DashboardMetadataRepository::class),
            $container->get(DashboardIdMappingRepository::class)
        );
    }
}
