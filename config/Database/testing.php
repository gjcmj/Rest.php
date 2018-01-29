<?php

return [
    'redis' => [
        'test' => ['host' => '127.0.0.1', 'port' => 6379, 'timeout' => 10, 'pconnect' => true],
    ],

    'mysql' => [
        'test' => [
            'dsn' => 'mysql:dbname=gxtimes;host=127.0.0.1;charset=UTF8',
            'username' => 'xxxxxx',
            'password' => 'xxxxxx',
            'options' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
                PDO::ATTR_PERSISTENT => false
            ]
        ]
    ]
];
