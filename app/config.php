<?php

use Ekok\Container\Box;
use Ekok\Sql\Connection;
use Ekok\Utils\Str;
use Ekok\Validation\Validator;

use function Ekok\Cosiler\Template\directory;

defined('ENV_') || define('ENV_', 'COSILER_ENV');

directory(__DIR__ . '/../templates');
storage(
    'fun',
    (new Box())
        ->load(
            __DIR__ . '/../env.dist.php',
            __DIR__ . '/../env.php',
            __DIR__ . '/../env.' . ($env = isset($_ENV[ENV_]) ? strtolower($_ENV[ENV_]) : null) .'.php',
        )
        ->with(static function (Box $box) use ($env) {
            $dir = Str::fixslashes(dirname(__DIR__));
            $tmp = $box['tmp_dir'] ?? ($dir . '/var');
            $con = $box['connection.' . $box['connection.default']];

            // create temporary directory if not exists
            is_dir($tmp) || mkdir($tmp, 0777, true);

            $box->allSet(array(
                'tmp_dir' => $tmp,
                'project_dir' => $dir,
                'env' => $env ?? $box['env'] ?? 'prod',
                'db' => static fn() => new Connection($con['dsn'], $con['username'] ?? null, $con['password'] ?? null, $con['options'] ?? null),
                'validator' => static fn() => new Validator(),
            ));
        }),
    true,
);
