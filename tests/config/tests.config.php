<?php

return [
    'db' => [
        'schema' => getenv('INTEGRATION_DB_DBNAME') ?: 'test',
        'dsn' => sprintf(
            'mysql:dbname=%s;host=%s;port=%d',
            getenv('INTEGRATION_DB_DBNAME') ?: 'test',
            getenv('INTEGRATION_DB_HOST') ?: 'test',
            (int)(getenv('INTEGRATION_DB_PORT') ?: 3306)
        ),
        'user' => getenv('INTEGRATION_DB_USER') ?: 'travis',
        'password' => getenv('INTEGRATION_DB_PASSWORD') ?: 'travis',
    ],
];
