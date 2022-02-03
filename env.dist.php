<?php

return array(
    'app' => array(
        'name' => 'My Applications',
        'alias' => 'MyApps',
        'year' => 2022,
        'owner' => 'MyCompany, Inc',
        'homepage' => 'http://example.com',
    ),
    'layout' => 'main',
    'connection' => array(
        'default' => 'sqlite',
        'mysql' => array(
            'dsn' => 'mysql:host=localhost;dbname=my_db',
            'username' => 'root',
            'password' => null,
            'options' => array('quotes' => '`'),
        ),
        'sqlite' => array(
            'dsn' => 'sqlite:' . __DIR__ . '/var/mydata.db',
        ),
    ),
);
