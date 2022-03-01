<?php

return [
    'debug' => true,

    'database' => [
        'host' => 'localhost',
        'user' => 'tuan',
        'pass' => '1408',
        'db_name' => 'php_test',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    ]
];
