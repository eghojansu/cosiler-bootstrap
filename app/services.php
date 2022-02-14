<?php

use Ekok\Logger\Log;
use Ekok\Container\Box;
use Ekok\Sql\Connection;
use Ekok\Validation\Validator;

return array(
    'db' => function(Box $box) {
        $con = $box['connection.' . $box['connection.default']];

        return new Connection($box['log'], $con['dsn'], $con['username'] ?? null, $con['password'] ?? null, $con['options'] ?? null);
    },
    'log' => fn(Box $box) => new Log(array(
        'directory' => $box['tmp_dir'] . '/logs',
        'threshold' => 'dev' === $box['env'] ? Log::LEVEL_DEBUG : Log::LEVEL_INFO,
    )),
    'validator' => fn () => (new Validator())
        ->addCustomRule('password', fn($password) => user_verify($password), 'This value should be current user password')
        ->addCustomRule('unique', function ($value, $table, $column = null, $key = null, $id = null) {
            $found = storage()['db']->selectOne($table, array(($column ?? $this->context->field). ' = ?', $value));

            return !$found || ($key && $id && $found[$key] == $id);
        }, 'This value is already used')
        ->addCustomRule('exists', function ($value, $table, $column = null) {
            return !!storage()['db']->selectOne($table, array(($column ?? $this->context->field). ' = ?', $value));
        }),
);
