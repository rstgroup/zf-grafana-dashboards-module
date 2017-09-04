<?php

return [
    'console'              => [
        'router' => [
            // ...
        ],
    ],
    'controllers'          => [
        'factories' => [
            \RstGroup\ZfGrafanaModule\Controller\DashboardMigrationsController::class      => \RstGroup\ZfGrafanaModule\Controller\DashboardMigrationsControllerFactory::class,
            \RstGroup\ZfGrafanaModule\Repository\DbalMetadataRepository::class             => \RstGroup\ZfGrafanaModule\Repository\DbalMetadataRepositoryFactory::class,
            \RstGroup\ZfGrafanaModule\DashboardMapping\MetadataBasedGrafanaIdMapper::class => \RstGroup\ZfGrafanaModule\DashboardMapping\MetadataBasedGrafanaIdMapperFactory::class,
            \RstGroup\ZfGrafanaModule\Repository\DbalIdMappingRepository::class            => \RstGroup\ZfGrafanaModule\Repository\DbalIdMappingRepositoryFactory::class,
        ],
        'aliases'   => [
            \RstGroup\ZfGrafanaModule\Dashboard\DashboardMetadataRepository::class         => \RstGroup\ZfGrafanaModule\Repository\DbalMetadataRepository::class,
            \RstGroup\ZfGrafanaModule\DashboardMapping\DashboardIdToGrafanaIdMapper::class => \RstGroup\ZfGrafanaModule\DashboardMapping\MetadataBasedGrafanaIdMapper::class,
        ],
    ],
    'service-manager'      => [
        'factories' => [
            \RstGroup\ZfGrafanaModule\Repository\FilesystemDirectoryRepository::class => \RstGroup\ZfGrafanaModule\Repository\FilesystemDirectoryRepositoryFactory::class,
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
