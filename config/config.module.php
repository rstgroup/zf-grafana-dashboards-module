<?php

return [
    'console'              => [
        'router' => [
            'routes' => [
                'migrate-dashboards' => [
                    'options' => [
                        'route'    => 'grafana migrate',
                        'defaults' => [
                            'controller' => \RstGroup\ZfGrafanaModule\Controller\DashboardMigrationsController::class,
                            'action'     => 'migrate',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers'          => [
        'factories' => [
            \RstGroup\ZfGrafanaModule\Controller\DashboardMigrationsController::class => \RstGroup\ZfGrafanaModule\Controller\DashboardMigrationsControllerFactory::class,
        ],
    ],
    'service_manager'      => [
        'factories' => [
            \RstGroup\ZfGrafanaModule\Repository\DbalMetadataRepository::class                      => \RstGroup\ZfGrafanaModule\Repository\DbalMetadataRepositoryFactory::class,
            \RstGroup\ZfGrafanaModule\Repository\DbalIdMappingRepository::class                     => \RstGroup\ZfGrafanaModule\Repository\DbalIdMappingRepositoryFactory::class,
            \RstGroup\ZfGrafanaModule\Repository\FilesystemDirectoryRepository::class               => \RstGroup\ZfGrafanaModule\Repository\FilesystemDirectoryRepositoryFactory::class,
            \RstGroup\ZfGrafanaModule\Controller\Helper\DirectoryListingDashboardIdsProvider::class => \RstGroup\ZfGrafanaModule\Controller\Helper\DirectoryListingDashboardIdsProviderFactory::class,
            \RstGroup\ZfGrafanaModule\Repository\GrafanaApiRepository::class                        => \RstGroup\ZfGrafanaModule\Repository\GrafanaApiRepositoryFactory::class,
            \RstGroup\ZfGrafanaModule\Grafana\GrafanaDashboardMapper::class                         => \RstGroup\ZfGrafanaModule\Grafana\GrafanaDashboardMapperFactory::class,
        ],
        'aliases'   => [
            \RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadataRepository::class         => \RstGroup\ZfGrafanaModule\Repository\DbalMetadataRepository::class,
            \RstGroup\ZfGrafanaModule\DashboardMapping\DashboardToDashboardMapper::class   => \RstGroup\ZfGrafanaModule\Grafana\GrafanaDashboardMapper::class,
            \RstGroup\ZfGrafanaModule\Controller\Helper\DashboardIdsProvider::class        => \RstGroup\ZfGrafanaModule\Controller\Helper\DirectoryListingDashboardIdsProvider::class,
            \RstGroup\ZfGrafanaModule\DashboardMapping\DashboardIdRepoToRepoMapper::class  => \RstGroup\ZfGrafanaModule\Repository\DbalIdMappingRepository::class,
            \RstGroup\ZfGrafanaModule\DashboardMapping\DashboardIdMappingRepository::class => \RstGroup\ZfGrafanaModule\Repository\DbalIdMappingRepository::class,
        ],
    ],
    'dashboard-migrations' => [
        'metadata-repositories' => [
            \RstGroup\ZfGrafanaModule\Repository\DbalMetadataRepository::class => [
                'table' => 'dashboard_metadata',
            ],
        ],
        'repositories'          => [
            \RstGroup\ZfGrafanaModule\Repository\FilesystemDirectoryRepository::class => [
                'path' => 'build/dashboards',
            ],
            \RstGroup\ZfGrafanaModule\Repository\GrafanaApiRepository::class          => [
                'url'     => 'http://rstgroup.grafana.com/api',
                'api-key' => null,
            ],
            \RstGroup\ZfGrafanaModule\Repository\DbalIdMappingRepository::class       => [
                'table' => 'dashboard_id_mapping',
            ],
        ],
        'ids-providers'         => [
            \RstGroup\ZfGrafanaModule\Controller\Helper\DirectoryListingDashboardIdsProvider::class => [
                'path' => 'build/dashboards',
            ],
        ],
        'source-repository'     => [
            'service' => \RstGroup\ZfGrafanaModule\Repository\FilesystemDirectoryRepository::class,
        ],
        'target-repository'     => [
            'service' => \RstGroup\ZfGrafanaModule\Repository\GrafanaApiRepository::class,
        ],
    ],
];
