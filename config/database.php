<?php

return [
    'fetch' => PDO::FETCH_OBJ,
    'default' => env('DB_CONNECTION', 'pgsql'),
    'migrations'=> 'migrations',
    'connections' => [
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'schema' => 'public'

        ],
        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => env('MONGODB_HOST', 'mongo'),
            'port'     => env('MONGODB_PORT', 55535),
            'database' => env('MONGODB_DATABASE'),
            'username' => env('MONGODB_USERNAME'),
            'password' => env('MONGODB_PASSWORD'),
            'options' => [
                'database' => 'hackair' // sets the authentication database required by mongo 3
            ]
        ]
    ]
];
