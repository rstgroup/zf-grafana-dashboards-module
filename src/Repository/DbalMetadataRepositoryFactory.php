<?php


namespace RstGroup\ZfGrafanaModule\Repository;

use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

/**
 * @codeCoverageIgnore
 */
final class DbalMetadataRepositoryFactory
{
    const SERVICE_CONNECTION = 'DbalMetadataRepository_Connection';

    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config')['dashboard-migrations']['metadata-repositories'][DbalMetadataRepository::class];

        Assert::keyExists($config, 'table');

        return new DbalMetadataRepository(
            $container->get(self::SERVICE_CONNECTION),
            $config['table']
        );
    }
}
