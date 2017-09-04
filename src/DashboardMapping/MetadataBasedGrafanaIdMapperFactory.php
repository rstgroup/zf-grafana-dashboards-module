<?php


namespace RstGroup\ZfGrafanaModule\DashboardMapping;

use Psr\Container\ContainerInterface;
use RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadataRepository;

/** @codeCoverageIgnore */
final class MetadataBasedGrafanaIdMapperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new MetadataBasedGrafanaIdMapper(
            $container->get(DashboardMetadataRepository::class)
        );
    }
}
