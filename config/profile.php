<?php

const DATETIME_FORMAT = 'Y-m-d H:i:s';

return [
    'site' => [
        'debug' => true,
        'rootDir' => __DIR__ . '/..',
    ],
    'log' => [
        'type' => 'file',
        'file' => __DIR__ . '/../logs/lightuna.log',
    ],
    'database' => [
        'type' => 'mariadb',
        'host' => 'db',
        'port' => 3306,
        'user' => 'lightuna',
        'password' => 'lightuna',
        'schema' => 'lightuna',
        'options' => [
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
        ]
    ],
];
