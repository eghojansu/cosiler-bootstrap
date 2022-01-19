<?php

return array(
    'app' => array(
        'name' => 'Application Name',
        'alias' => 'AppsName',
        'year' => 2022,
        'owner' => 'MyCompany, Inc',
        'homepage' => 'http://example.com',
    ),
    'db_setup' => array(
        'mysql' => array(
            'dsn' => 'mysql:host=localhost;dbname=my_db',
            'username' => 'root',
            'password' => null,
            'options' => null,
        ),
        'sqlite' => array(
            'dsn' => 'sqlite::memory:',
        ),
    ),
);
