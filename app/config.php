<?php

use Ekok\Container\Box;
use Ekok\Cosiler\Template;
use Ekok\Sql\Connection;
use Ekok\Utils\Str;
use Ekok\Validation\Validator;

Template\directory(__DIR__ . '/../templates');
storage(
    'fun',
    (new Box())
        ->load(__DIR__ . '/../env.php', __DIR__ . '/../env.dev.php')
        ->with(static function (Box $box) {
            $tmp = $box['project_dir'] . '/var';
            $db = $box['connection.' . $box['connection.default']];

            // create temporary directory if not exists
            is_dir($tmp) || mkdir($tmp, 0777, true);

            $box->allSet(array(
                'db' => new Connection($db['dsn'], $db['username'] ?? null, $db['password'] ?? null, $db['options'] ?? null),
                'project_dir' => Str::fixslashes(dirname(__DIR__)),
                'tmp_dir' => $tmp,
                'validator' => fn() => new Validator(),
            ));
        }),
    true,
);
