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
        $groups = get_all('menu', null, array('columns' => '"distinct grp'));
        /** @var \PDOStatement */
        $query = null;
        $get = static function () use (&$query) {
            $values = array($group, $parent);

            if ($query) {
                $query->execute($values);
            } else {
                db()->query('SELECT * FROM menu WHERE ACTIVE = 1 AND grp = ? AND parentid = ?', $values, $query);
            }


        };
        $menu = array_reduce($groups, static function (array|null $menu, array $row) use (&$query) {
            $group = $row['grp'] ?? 'main';
        });
        dump($groups);
        // $query = db()->query('select * from menu where active = 1 and parentid =')
        $rows = get_all('menu', 'active = 1', array('orders' => 'parentid'));
        $getRef = static function &(array &$grouped, string $parent, $getRef) {
            $null = null;

            foreach ($grouped as $gid => $gval) {
                if (!isset($gval['items'])) {
                    'MRuPYseS' === $gid || dump('x', $gval);
                    continue;
                }

                if (isset($gval['items'][$parent])) {
                    return $grouped[$gid]['items'][$parent];
                }

                $item = &$getRef($gval, $parent, $getRef);
                unset($gval);

                if ($item) {
                    return $item;
                }
            }

            return $null;
        };
        $menu = array_reduce($rows, static function (array $menu, array $row) use ($getRef) {
            $id = $row['menuid'];
            $path = $row['path'] ?? '#';
            $group = $row['grp'] ?? 'main';
            $title = Str::caseTitle($row['title'] ?? str_replace(array('#', '/', '-'), ' ', $row['path'] ?? $id));
            $roles = $row['roles'] ? array_filter(explode(',', $row['roles']), 'trim') : array();
            $prompt = isset($path[1]) && '#' === substr($path, -1);

            if (isset($path[1])) {
                $path = trim($path, '#/');
            }

            if ($row['parentid']) {
                if (isset($menu[$row['parentid']])) {
                    $item = &$menu[$row['parentid']];
                } else {
                    $item = &$getRef($menu, $row['parentid'], $getRef);
                }

                if ($roles) {
                    array_push($item['roles'], ...$roles);
                }

                $item = &$item['items'][$id];
            } else {
                $item = &$menu[$id];
            }

            $item = compact('id', 'path', 'prompt', 'roles', 'title') + array(
                'icon' => $row['icon'],
                'description' => $row['description'],
                'parent' => $row['parentid'],
                'data' => $row['data'] ? json_decode($row['data']) : array(),
            );

            return $menu;
        }, array());

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
