<?php

use Ekok\Utils\Str;
use Ekok\Utils\Val;
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
    'menu' => function(Box $box) {
        /** @var PDOStatement */
        $query = null;
        $getMenu = static function (array|null $parent, Closure $getMenu) use ($query) {
            $pid = $parent['id'] ?? null;
            $values = array($pid, $pid);

            if ($query) {
                $query->execute($values);
            } else {
                $sql = 'SELECT * FROM menu WHERE deleted_at IS NULL AND ACTIVE = 1 AND ((? IS NULL AND parentid IS NULL) OR (parentid = ?))';

                db()->query($sql, $values, $query);
            }

            return array_reduce(
                $query->fetchAll(PDO::FETCH_ASSOC),
                static function (array|null $menu, array $row) use ($parent, $getMenu) {
                    $id = $row['menuid'];
                    $path = $row['path'] ?? '#';
                    $title = Str::caseTitle($row['title'] ?? str_replace(array('#', '/', '-'), ' ', $row['path'] ?? $id));
                    $roles = $row['roles'] ? array_filter(explode(',', $row['roles']), 'trim') : array();
                    $prompt = isset($path[1]) && '#' === substr($path, -1);

                    if ('#' !== $path[0] && '#' !== ($parent['path'][0] ?? '#')) {
                        $path = $parent['path'] . '/' . ltrim($path, '/');
                    }

                    if (isset($path[1])) {
                        $path = trim($path, '#/');
                    }

                    $menu[$id] = compact('id', 'path', 'prompt', 'roles', 'title') + array(
                        'icon' => $row['icon'],
                        'description' => $row['description'],
                        'parent' => $row['parentid'],
                        'data' => $row['data'] ? json_decode($row['data']) : array(),
                    );
                    $menu[$id]['items'] = $getMenu($menu[$id], $getMenu);

                    return $menu;
                },
            );
        };
        $menu = array_reduce(
            get_all('menu', 'root = 1', array('columns' => array('id' => 'menuid', 'path'))),
            static fn (array $menu, array $group) => $menu + array($group['id'] => $getMenu($group, $getMenu)),
            array(),
        );

        return $menu;
    },
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
