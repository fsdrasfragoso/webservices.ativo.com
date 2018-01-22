<?php

return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'read' => [
                'host' => env('DB_READ_HOST'),
            ],
            'write' => [
                'host' => env('DB_WRITE_HOST'),
            ],
            'driver' => 'mysql',
            'host' => env('DB_READ_HOST'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ]
    ]
];
