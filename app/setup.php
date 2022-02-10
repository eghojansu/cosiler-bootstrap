<?php

use Ekok\Container\Box;
use Ekok\Utils\Str;

use function Ekok\Cosiler\Http\set_entry;
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
            __DIR__ . '/services.php',
        )
        ->loadInto('choice.', __DIR__ . '/choices.php')
        ->with(static function (Box $box) use ($env) {
            $dir = Str::fixslashes(dirname(__DIR__));
            $tmp = $box['tmp_dir'] ?? ($dir . '/var');

            // create temporary directory if not exists
            is_dir($tmp) || mkdir($tmp, 0777, true);

            $box->allSet(array(
                'tmp_dir' => $tmp,
                'project_dir' => $dir,
                'env' => $env ?? $box['env'] ?? 'prod',
            ));

            set_entry($box['entry']);
        }),
    true,
);
