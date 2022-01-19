<?php

require __DIR__ . '/../vendor/autoload.php';

Ekok\Cosiler\bootstrap(
    __DIR__ . '/error.php',
    __DIR__ . '/functions.php',
    __DIR__ . '/macros.php',
    __DIR__ . '/config.php',
    __DIR__ . '/routes.php',
);
