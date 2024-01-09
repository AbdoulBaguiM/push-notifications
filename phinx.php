<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/database/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_environment' => 'local',
        'local' => [
            'charset' => config('DB_CHARSET'),
            'adapter' => config('DB_ADAPTER'),
            'host' => config('DB_HOST'),
            'port' => config('DB_PORT'),
            'name' => config('DB_NAME'),
            'user' => config('DB_USER_NAME'),
            'pass' => config('DB_PASSWORD'),
        ],
    ],
    'version_order' => 'creation'
];
