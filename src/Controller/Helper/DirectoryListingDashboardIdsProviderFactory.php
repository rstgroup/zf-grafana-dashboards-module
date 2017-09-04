<?php


namespace RstGroup\ZfGrafanaModule\Controller\Helper;

use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

/**
 * @codeCoverageIgnore
 */
final class DirectoryListingDashboardIdsProviderFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var array $config */
        $config = $container->get('config')['dashboard-migrations']['ids-providers'][DirectoryListingDashboardIdsProvider::class];

        Assert::isArray($config);
        Assert::keyExists($config, 'path');

        return new DirectoryListingDashboardIdsProvider($config['path']);
    }
}
