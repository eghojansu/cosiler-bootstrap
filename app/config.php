<?php

use Ekok\Container\Box;
use Ekok\Sql\Connection;
use Ekok\Sql\ModifiableBuilder;
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
                'db' => static fn() => ($db = new Connection($con['dsn'], $con['username'] ?? null, $con['password'] ?? null, $con['options'] ?? null))->setBuilder(
                    (new ModifiableBuilder($db->getHelper(), $db->getDriver(), $db->getOptions()['format_query']))
                        ->addModifier('insert', fn($table, $data) => array($table, $data + array('created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'created_by' => user_id(), 'updated_by' => user_id())))
                        ->addModifier('update', fn($table, $data, $criteria) => array($table, $data + array('updated_at' => date('Y-m-d H:i:s'), 'updated_by' => user_id()), $criteria))
                        ->addModifier('delete', function ($table, $criteria, ModifiableBuilder $builder) {
                            if ($criteria && false !== strpos(is_string($criteria) ? $criteria : $criteria[0], 'deleted_at is')) {
                                return array($table, $criteria);
                            }

                            return array($table, $builder->mergeCriteria($criteria, 'deleted_at is null'));
                        })
                        ->addModifier('select', function ($table, $criteria, $options, ModifiableBuilder $builder) {
                            if ($criteria && false !== strpos(is_string($criteria) ? $criteria : $criteria[0], 'deleted_at is')) {
                                return array($table, $criteria, $options);
                            }

                            return array($table, $criteria, $builder->mergeCriteria($criteria, 'deleted_at is null'));
                        })
                ),
                'validator' => static fn() => new Validator(),
            ));
        }),
    true,
);
