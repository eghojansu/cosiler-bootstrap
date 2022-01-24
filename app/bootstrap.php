<?php

require __DIR__ . '/../vendor/autoload.php';


$_SERVER['HTTP_X_CODECEPTION_CODECOVERAGE_DEBUG'] = 1;
$_SERVER['HTTP_X_CODECEPTION_CODECOVERAGE'] = 'absurd';
require __DIR__ . '/../tests/scripts/c3.php';

Ekok\Cosiler\bootstrap(
    __DIR__ . '/error.php',
    __DIR__ . '/functions.php',
    __DIR__ . '/macros.php',
    __DIR__ . '/config.php',
    __DIR__ . '/routes.php',
);
