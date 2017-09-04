<?php


namespace RstGroup\ZfGrafanaModule\Controller;

use Psr\Container\ContainerInterface;

/** @codeCoverageIgnore */
final class DashboardMigrationsControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new DashboardMigrationsController();
    }
}
