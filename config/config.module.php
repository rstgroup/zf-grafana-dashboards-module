<?php

return [
    'console'              => [
        'router' => [
            'routes' => [
                'migrate-dashboards' => [
                    'options' => [
                        'route' => 'grafana migrate',
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
    'service-manager'      => [
        'factories' => [
            \RstGroup\ZfGrafanaModule\Repository\DbalMetadataRepository::class             => \RstGroup\ZfGrafanaModule\Repository\DbalMetadataRepositoryFactory::class,
            \RstGroup\ZfGrafanaModule\Repository\DbalIdMappingRepository::class            => \RstGroup\ZfGrafanaModule\Repository\DbalIdMappingRepositoryFactory::class,
            \RstGroup\ZfGrafanaModule\Repository\FilesystemDirectoryRepository::class      => \RstGroup\ZfGrafanaModule\Repository\FilesystemDirectoryRepositoryFactory::class,
        ],
        'aliases'   => [
            \RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadataRepository::class         => \RstGroup\ZfGrafanaModule\Repository\DbalMetadataRepository::class,
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
        ],
        'source-repository' => [
            'service' => \RstGroup\ZfGrafanaModule\Repository\FilesystemDirectoryRepository::class,
        ],
        'target-repository' => [
            'service' => \RstGroup\ZfGrafanaModule\Repository\GrafanaApiRepository::class,
        ],
    ],
];
