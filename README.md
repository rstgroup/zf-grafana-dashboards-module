# ZF Grafana Module

This module contains integration with Grafana tool via it's [HTTP APIs](http://docs.grafana.org/http_api/).

## Installation

```bash
composer require rstgroup/zf-grafana-module
```

..and then add the module to your app using ZF system configuration (`config/application.config.php`):
```php
return [
    'modules' => [
        'RstGroup\ZfGrafanaModule',
    ],
]
```

The last step is providing database connection and HTTP client to allow the library to communicate with other services.
Use aliasing functionality of Zend's Service Manager to define:
* `\RstGroup\ZfGrafanaModule\Repository\GrafanaApiRepositoryFactory::HTTP_CLIENT_SERVICE` <br />
    HTTP client that implements `Http\Client\HttpClient` interface 
* `\RstGroup\ZfGrafanaModule\Repository\GrafanaApiRepositoryFactory::REQUEST_FACTORY_SERVICE` <br />
    request factory used to create HTTP requests
* `\RstGroup\ZfGrafanaModule\Repository\DbalMetadataRepositoryFactory::SERVICE_CONNECTION` <br />
    Doctrine DBAL Connection to database where the dashboard's metadata will be stored
* `\RstGroup\ZfGrafanaModule\Repository\DbalIdMappingRepositoryFactory::SERVICE_CONNECTION` <br />
    Doctrine DBAL Connection to database where the dashboard's ID mapping will be stored

## Dashboards

Module gives you functionality to automatically synchronize your dashboards with given Grafana instance.
  
### Usage

Module provides CLI command:

```bash
php public/index.php grafana migrate
```
  
There is currently no param required. All the information about dashboards is fetched from app's configuration.
  
### Defining dashboards to sync

By default, script scans through `build/dashboards` directory, looking for `.json` files with dashboard definition.

The directory to search can be easily changed in config file, here's the example:

```php
return [
    'dashboard-migrations' => [
        'repositories' => [
            \RstGroup\ZfGrafanaModule\Repository\FilesystemDirectoryRepository::class => [
                'path' => 'path/to/your/directory',
            ],
        ],
    ],
];
```

### Custom dashboard definition source

It's also possible to store dashboard definitions elsewhere. To do it, you need to do two things:
 
* Implement your own `DashboardRepository`, define it in Zend's Service Manager and configure module to use it:
 
    ```php
    return [
        'service_manager' => [
            'factories' => [
                MyOwnRepository::class => MyOwnRepositoryFactory::class,
            ]
        ],
        'dashboard-migrations' => [
            'source-repository' => [
                'service' => MyOwnRepository::class,
            ]
        ]
    ]
    ```
     
* Implement your own `DashboardIdsProvider`, that will return the IDs to fetch from your custom repository.
Then just alias your custom provider by the interface name:

    ```php
    return [
        'service_manager' => [
            'aliases' => [
                \RstGroup\ZfGrafanaModule\Controller\Helper\DashboardIdsProvider::class => \Your\Custom\Provider::class,
            ];
        ]
    ];
    ```


### Defining remote repository
To make synchronizing work, you need to pass Grafana API basic URL and API key. These values should be passed to app's
configuration via config files (or, better, in Consul, if your app can fetch configuration from it!):

```php
return [
    'dashboard-migrations' => [
        'repositories' => [
            \RstGroup\ZfGrafanaModule\Repository\GrafanaApiRepository::class => [
                'url'     => 'http://url.to.grafana.com/api',
                'api-key' => 'grafana-api-key',
            ],
        ],
    ],
];
```

The next thing you need is HTTP Client (which implements [PSR's client interface](http://docs.php-http.org/en/latest/clients.html)) 
and HTTP message factory implementation (see [http://docs.php-http.org/en/latest/message/message-factory.html]())

You should pass those in you app's configuration, aliasing predefined service names, like in the example below:

```php
return [
    'service_manager' => [
        'aliases' => [
            \RstGroup\ZfGrafanaModule\Repository\GrafanaApiRepositoryFactory::HTTP_CLIENT_SERVICE       => \Http\Adapter\Guzzle6\Client::class,
            \RstGroup\ZfGrafanaModule\Repository\GrafanaApiRepositoryFactory::REQUEST_FACTORY_SERVICE => \Http\Message\MessageFactory\GuzzleMessageFactory::class,
        ]
    ]
];
```

### Metadata

#### Why is there any metadata?

The module needs to store Dashboard's metadata. It's because of Grafana API, which generates additional identifiers
and parameters for published dashboards.

First of these is dashboard's <strong>SLUG</strong>. The slug is a textual, URL-safe representation of dashboard's Title. Slug is used in
API as the required parameter in GET requests and thus can be trated as identifier.

Second one is  Dashboard's <strong>ID</strong>, generated right after dashboard creation. The ID is a positive integer. It is used in update
request (`POST`) and has to be provided as dashboard definition (inside of JSON), thus also can be treat as dashboard's 
identifier - the second, less important one :))

The next metadata parameter is dashboard's <strong>VERSION</strong> - after each update, the version is incremented. If you try to update
your dashboard with the one with lower version number - API will refuse the change.
 
The last parameter is <strong>SCHEMA VERSION</strong>, which determines the version of definition schema itself, so
Grafana instances are able to determine if it's up-to-date enough to parse given dashboard definition.

#### Storage
 
By default - metadata is stored in MySQL database, thus the Doctrine DBAL Connection should be aliased for mapper to work:

```php
return [
    'service_manager' => [
        'aliases' => [
            \RstGroup\ZfGrafanaModule\Repository\DbalIdMappingRepositoryFactory::SERVICE_CONNECTION => 'Your\Doctrine\Dbal\Connection'
        ]
    ]
]
```

The table should follow the definition below:

```mysql
CREATE TABLE dashboard_metadata (
  dashboard_id VARCHAR(255) NOT NULL PRIMARY KEY,
  grafana_id INT NOT NULL,
  dashboard_version INT NOT NULL,
  dashboard_schema_version INT DEFAULT NULL
) DEFAULT CHARACTER SET 'utf8';
```

#### ID mapping

Because the SLUG is created on the Grafana API's side, there is a need for keeping
the local -> remote ID mapping.

By default, the local identifier of dashboard is its definition's filename. Mapping is stored
in the database, thus the Doctrine DBAL Connection should be aliased for mapper to work:

```php
return [
    'service_manager' => [
        'aliases' => [
            \RstGroup\ZfGrafanaModule\Repository\DbalIdMappingRepositoryFactory::SERVICE_CONNECTION => 'Your\Doctrine\Dbal\Connection'
        ]
    ]
]
```

The mapping table should follow the definition:

```mysql
CREATE TABLE dashboard_id_mapping (
  local_id VARCHAR(255) NOT NULL PRIMARY KEY,
  remote_id VARCHAR(255) NOT NULL
) DEFAULT CHARACTER SET 'utf8';
```
