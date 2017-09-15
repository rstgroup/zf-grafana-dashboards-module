<?php


namespace RstGroup\ZfGrafanaModule\Repository\GrafanaApi;

use Psr\Container\ContainerInterface;
use RstGroup\ZfGrafanaModule\Grafana\GrafanaDashboardMapper;
use RstGroup\ZfGrafanaModule\Grafana\RequestHelper;
use RstGroup\ZfGrafanaModule\Grafana\ResponseHelper;
use Webmozart\Assert\Assert;

/** @codeCoverageIgnore */
final class DashboardApiRepositoryFactory
{
    const HTTP_CLIENT_SERVICE = 'GrafanaApiRepository_HttpClient';
    const REQUEST_FACTORY_SERVICE = 'GrafanaApiRepository_RequestFactory';

    public function __invoke(ContainerInterface $container)
    {
        // make sure config exists
        Assert::true(
            isset($container->get('config')['dashboard-migrations']['repositories'][DashboardApiRepository::class]),
            sprintf('Configuration for %s is not provided.', DashboardApiRepository::class)
        );

        $config = $container->get('config')['dashboard-migrations']['repositories'][DashboardApiRepository::class];

        // make sure config has required keys
        Assert::isArray($config);
        Assert::keyExists($config, 'url', sprintf('Missing %%s param in %s configuration', DashboardApiRepository::class));
        Assert::keyExists($config, 'api-key', sprintf('Missing %%s param in %s configuration', DashboardApiRepository::class));

        return new DashboardApiRepository(
            $container->get(self::HTTP_CLIENT_SERVICE),
            new RequestHelper(
                $container->get(self::REQUEST_FACTORY_SERVICE),
                $config['url'],
                $config['api-key']
            ),
            new ResponseHelper(),
            $container->get(GrafanaDashboardMapper::class)
        );
    }
}
