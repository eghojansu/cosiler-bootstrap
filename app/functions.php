<?php

use Ekok\Cosiler;
use Ekok\Utils\Str;
use Ekok\Cosiler\Http;
use Ekok\Sql\Connection;
use Ekok\Cosiler\Template;
use Ekok\Cosiler\Http\Request;
use Ekok\Cosiler\Http\Response;

// redefine: register functions as globals

function storage(string $name = null, ...$sets) {
    return Cosiler\storage($name, ...$sets);
}

function path(string $path = null): string {
    return Http\path($path);
}

function current_path(): string {
    return Request\path();
}

function current_method(): string {
    return Request\method();
}

function asset(string $path): string {
    return path('/assets/' . ltrim($path, '/'));
}

function redirect(string $path, string $message = null): void {
    if ($message) {
        message_commit($message);
    }

    Response\redirect($path);
}

function back(string $key = null, $value = null, bool $continue = false): void {
    Response\back($key, $value, $continue);
}

function session(string $key = null, $value = null) {
    return Http\session($key, $value);
}

function flash(string $key = null) {
    return Http\flash($key);
}

function load(string $template, array $data = null): void {
    Template\load($template, $data);
}

function not_found(string $message = null, array $payload = null, array $headers = null): void {
    Http\not_found($message ?? 'The requested page does not exists', $payload, $headers);
}

function forbidden(string $message = null, array $payload = null, array $headers = null): void {
    Http\forbidden($message, $payload, $headers);
}

function unprocessable(string $message = null, array $payload = null, array $headers = null): void {
    Http\unprocessable($message, $payload, $headers);
}

function bad_request(string $message = null, array $payload = null, array $headers = null): void {
    Http\bad_request($message, $payload, $headers);
}

function get(string $key = null) {
    return Request\get($key);
}

function post(string $key = null) {
    return Request\post($key);
}

function ignore_deleted(array|null $options): string {
    $alias = isset($options['alias']) ? $options['alias'] . '.' : null;

    return $alias . 'deleted_at is null';
}

function extra_save(bool $new = false): array {
    $extra = array('updated_at' => timestamp(), 'updated_by' => user_id());

    if ($new) {
        $extra += array('created_at' => $extra['updated_at'], 'created_by' => $extra['updated_by']);
    }

    return $extra;
}

function extra_delete(): array {
    return array('deleted_at' => timestamp(), 'deleted_by' => user_id());
}

function paginate(string $table, array|string $criteria = null, array $options = null, int $page = null): array {
    $db = db();

    return $db->paginate($table, $page ?? get_int('page'), $db->builder->criteriaMerge($criteria, ignore_deleted($options)), $options);
}

function get_all(string $table, array|string $criteria = null, array $options = null): array|null {
    $db = db();

    return $db->select($table, $db->builder->criteriaMerge($criteria, ignore_deleted($options)), $options);
}

function get_one(string $table, array|string $criteria = null, array $options = null): array|null {
    $db = db();

    return $db->selectOne($table, $db->builder->criteriaMerge($criteria, ignore_deleted($options)), $options);
}

function get_count(string $table, array|string $criteria = null, array $options = null): int {
    $db = db();

    return $db->count($table, $db->builder->criteriaMerge($criteria, ignore_deleted($options)), $options);
}

function save(string $table, array $data, array|string $criteria = null, array|bool|null $options = false): bool|int|array|object|null {
    if (null === $criteria) {
        return db()->insert($table, $data + extra_save(true), $options);
    }

    return db()->update($table, $data + extra_save(), $criteria, $options);
}

function save_batch(string $table, array $data, array|string $criteria = null, array|string $options = null): bool|int|array|null {
    $extra = extra_save(true);

    return db()->insertBatch($table, array_map(static fn(array $row) => $row + $extra, $data), $criteria, $options);
}

function delete(string $table, array|string $criteria, bool $soft = true): bool|int {
    if ($soft) {
        return save($table, extra_delete(), $criteria);
    }

    return db()->delete($table, $criteria);
}

function query(string $sql, array $values = null, \PDOStatement &$query = null): bool {
    return db()->query($sql, $values, $query);
}

// extends

function db(): Connection {
    return storage()->db;
}

function get_int(string $key, int $default = 1): int {
    return intval(get($key) ?? $default, 0);
}

function not_found_if(bool|callable $condition, string $message = null, ...$args): void {
    if (is_true($condition, ...$args)) {
        not_found($message);
    }
}

function redirect_saved(string $path): void {
    redirect($path, 'Data has been saved');
}

function redirect_deleted(string $path): void {
    redirect($path, 'Data has been deleted');
}

// apps

function env(): string {
    return storage()->env ?? 'prod';
}

function env_is(string ...$envs): bool {
    return in_array(strtolower(env()), array_map('strtolower', $envs));
}

function is_installed(string &$versionFile = null): bool {
    $versionFile = storage()->tmp_dir . '/version.txt';

    return !env_is('dev') && is_file($versionFile);
}

function model(string $name) {
    if (class_exists($class = 'App\\Model\\' . $name) || class_exists($class = 'App\\Model\\' . Str::casePascal($name))) {
        return new $class(db());
    }

    throw new \LogicException(sprintf('Unable to resolve model: %s', $name));
}

// database

function timestamp(): string {
    return date('Y-m-d H:i:s');
}

// auth and messaging

function user_id(): string|null {
    return user('userid');
}

function user_verify(string $password, string $hash = null): bool {
    return ($check = $hash ?? user('password') ?? null) && password_verify($password, $check);
}

function user_password(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT);
}

function user(string $key = null): array|string|bool|null {
    static $user = false;

    if (false === $user) {
        if (($bearer = Request\bearer()) && false !== strpos($bearer, ':')) {
            list($sessid, $token) = explode(':', $bearer);

            $sess = get_one('user_session', array('sessid = ?', $sessid));

            if (!$sess || !password_verify($token, $sess['token'])) {
                bad_request();
            }

            if (!$sess['active']) {
                forbidden('Your session has been expired');
            }

            $userid = $sess['userid'];
        }

        $user = ($id = $userid ?? session('user')) ? get_one('user', array('userid = ?', $id)) : null;

        if ($user) {
            if (!$user['active']) {
                forbidden('Your account was inactive');
            }

            $user['sess'] = $sess ?? null;
            $user['roles'] = explode(',', $user['roles']);
            $user['roles'][] = 'user';
            $user['active'] = !!$user['active'];
        }
    }

    return $key ? ($user[$key] ?? null) : $user;
}

function user_commit($userid): void {
    session('user', $userid);
}

function user_commit_session($userid): string {
    $sessid = Str::random(8);
    $token = Str::random(16);
    $hash = user_password($token);

    save('user_session', array(
        'active' => 1,
        'token' => $hash,
        'userid' => $userid,
        'sessid' => $sessid,
        'ip_address' => Request\ip_address(),
        'user_agent' => Request\user_agent(),
        'recorded_at' => timestamp(),
        'device_id' => user_device_id(),
    ));

    return $sessid . ':' . $token;
}

function user_device_id(): string {
    return $_SERVER['HTTP_X_DEVICE_ID'] ?? Request\user_agent();
}

function has_role(string|array $roles, string $sessid = null): bool {
    return ($check = user('roles', $sessid)) && 0 < count(array_intersect($check, (array) $roles));
}

function logout(string $target = null): void {
    Http\session_end();
    redirect($target ?? '/');
}

function is_guest(): bool {
    return !user_id();
}

function guard(string|array $roles = null, string $target = null): void {
    if (is_guest() || ($roles && !has_role($roles))) {
        if (Request\wants_json()) {
            forbidden();
        }

        redirect($target ?? 'login');
    }
}

function guest(string $target = null): void {
    if (!is_guest()) {
        if (Request\wants_json()) {
            forbidden();
        }

        redirect($target ?? '/');
    }
}

function message() {
    return flash('message');
}

function message_commit(string $message): void {
    session('message', $message);
}

function data() {
    return flash('data');
}

function data_commit(array $data = null): void {
    session('data', $data ?? array());
}

function error() {
    return flash('error');
}

function error_commit(string $message, array $errors = null, array $data = null): void {
    data_commit($data);
    session('error', array(
        'message' => $message,
        'errors' => array_map(fn(array $group) => implode(', ', $group), $errors ?? array()),
    ));
}

function record(string $activity, bool $visible = true, string $url = null, array $data = null): void {
    try {
        save('user_activity', ($data ?? array()) + array(
            'userid' => user_id(),
            'activity' => $activity,
            'visible' => $visible ? 1 : 0,
            'url' => $url ?? (Request\method() . ' ' . Request\uri()),
            'ip_address' => Request\ip_address(),
            'user_agent' => Request\user_agent(),
            'recorded_at' => timestamp(),
        ));
    } catch (\Throwable $e) {}
}

// content

function validate(array $rules, array $data = null): array {
    return storage()->validator->validate($rules, $data ?? post())->getData();
}

function validate_json(array $rules): array {
    return validate($rules, Request\json());
}

function is_true(bool|callable $condition, ...$args): bool {
    return is_bool($condition) ? $condition : !!$condition(...$args);
}

function file_mime(string $file, string $default = null, bool $ext = false): string {
    static $mimes = array(
        'css' => 'text/css',
        'gif' => 'image/gif',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'application/x-javascript',
        'png' => 'image/png',
        'svg' => 'text/xml-svg',
        'txt' => 'text/plain',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
    );

    return $mimes[$ext ? $file : strtolower(ltrim(strrchr($file, '.'), '.'))] ?? $default ?? 'application/octet-stream';
}

// templating

function layout(string $layout): void {
    storage()->layout = $layout;
}

function layout_none(): void {
    layout('');
}

function layout_used(): string {
    return storage()->layout ?? 'base';
}

function script_add(string ...$scripts): void {
    storage()->push('scripts', ...$scripts);
}

function module_add(string ...$modules): void {
    storage()->push('modules', ...$modules);
}

function style_add(string ...$styles): void {
    storage()->push('styles', ...$styles);
}

function scripts(): array {
    return storage()->scripts ?? array();
}

function modules(): array {
    return storage()->modules ?? array();
}

function styles(): array {
    return storage()->styles ?? array();
}

function menu_active(string $path): void {
    storage()->menu_active = '/' . ltrim($path, '/');
}

// debugging

function dump(...$values): void {
    ob_end_clean();
    print('<pre>');
    var_dump(...$values);
    print('</pre>');
    die;
}

// apis
function api(string $message, array $data = null, bool $success = true): void {
    $content = compact('success', 'message');

    if ($data) {
        $content[$success ? 'data' : 'errors'] = $data;
    }

    Response\json($content);
    exit;
}

function api_fail(string $message, array $errors = null): void {
    api($message, $errors, false);
}
