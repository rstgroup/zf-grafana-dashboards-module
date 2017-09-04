<?php


namespace RstGroup\ZfGrafanaModule\Repository;

use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

/** @codeCoverageIgnore */
final class FilesystemDirectoryRepositoryFactory
{
    public function __invoke(ContainerInterface $container)
    {
        // make sure config exists
        Assert::true(
            isset($container->get('config')['dashboard-migrations']['repositories'][FilesystemDirectoryRepository::class]),
            sprintf('Configuration for %s is not provided.', FilesystemDirectoryRepository::class)
        );

        $config = $container->get('config')['dashboard-migrations']['repositories'][FilesystemDirectoryRepository::class];

        // make sure config has required keys
        Assert::isArray($config);
        Assert::keyExists($config, 'path', sprintf('Missing %%s param in %s configuration', FilesystemDirectoryRepository::class));

        return new FilesystemDirectoryRepository($config['path']);
    }
}
