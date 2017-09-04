<?php


namespace RstGroup\ZfGrafanaModule\Repository;

use Doctrine\DBAL\Driver\Connection;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

/**
 * @codeCoverageIgnore
 */
final class DbalMetadataRepositoryFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config')['dashboard-migrations']['metadata-repositories'][DbalMetadataRepository::class];

        Assert::keyExists($config, 'table');

        return new DbalMetadataRepository(
            $container->get(Connection::class),
            $config['table']
        );
    }
}
