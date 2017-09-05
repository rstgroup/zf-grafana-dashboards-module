<?php


namespace RstGroup\ZfGrafanaModule\Repository;

use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

/**
 * @codeCoverageIgnore
 */
final class DbalIdMappingRepositoryFactory
{
    const SERVICE_CONNECTION = 'DbalIdMappingRepository_Connection';

    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config')['dashboard-migrations']['repositories'][DbalIdMappingRepository::class];

        Assert::keyExists($config, 'table');

        return new DbalIdMappingRepository(
            $container->get(self::SERVICE_CONNECTION),
            $config['table']
        );
    }
}
