<?php

use Ekok\Cosiler;
use Ekok\Cosiler\Http;
use Ekok\Cosiler\Http\Response;
use Ekok\Cosiler\Http\Request;
use Ekok\Cosiler\Template;

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

function asset(string $path): string {
    return path('/assets/' . ltrim($path, '/'));
}

function redirect(string $path, bool $continue = false): void {
    Response\redirect($path, $continue);
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

function logout(string $target = null): void {
    Http\session_end();
    redirect($target ?? '/');
}

function load(string $template, array $data = null): void {
    Template\load($template, $data);
}

function not_found(string $message = null, array $payload = null, array $headers = null): void {
    Http\not_found($message ?? 'The requested page does not exists', $payload, $headers);
}

function get(string $key = null) {
    return Request\get($key);
}

function post(string $key = null) {
    return Request\post($key);
}

// extends

function get_int(string $key, int $default = 1): int {
    return intval(get($key) ?? $default, 0);
}

function not_found_if(bool|callable $condition, string $message = null, ...$args): void {
    if (is_true($condition, ...$args)) {
        not_found($message);
    }
}

// customs

function env(): string {
    return storage()['env'] ?? 'prod';
}

function env_is(string ...$envs): bool {
    return in_array(strtolower(env()), array_map('strtolower', $envs));
}

function is_guest(): bool {
    return !session('user');
}

function user(): array|null {
    static $user = false;

    if (false === $user) {
        $id = session('user');

        $user = $id ? storage()['db']->selectOne('user', array('userid = ?', $id)) : null;

        if ($user) {
            $user['roles'] = explode(',', $user['roles']);
            $user['active'] = !!$user['active'];
        }
    }

    return $user;
}

function userCommit($id): void {
    session('user', $id);
}

function has_role(string|array $roles): bool {
    return 0 < count(array_intersect(user()['roles'], (array) $roles));
}

function guard(string|array $roles = null, string $target = null): void {
    if (is_guest() || ($roles && !has_role($roles))) {
        Response\redirect($target ?? 'login');
    }
}

function guest(string $target = null): void {
    is_guest() || redirect($target ?? storage()['home_route'] ?? '/');
}

function message() {
    return flash('message');
}

function messageCommit(string $message): void {
    session('message', $message);
}

function data() {
    return flash('data');
}

function dataCommit(array $data = null): void {
    session('data', $data ?? array());
}

function error() {
    return flash('error');
}

function errorCommit(string $message, array $errors = null, array $data = null): void {
    dataCommit($data);
    session('error', array(
        'message' => $message,
        'errors' => array_map(fn(array $group) => implode(', ', $group), $errors ?? array()),
    ));
}

function layout(string $layout): void {
    storage()['layout'] = $layout;
}

function layout_none(): void {
    layout('');
}

function layout_used(): string {
    return storage()['layout'] ?? 'base';
}

function validate(array $rules, array $data = null): array {
    return storage()['validator']->validate($rules, $data ?? post())->getData();
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

function format_trace_frame(array $frame) {
    if (false !== strpos($frame['function'], '{closure}')) {
        return '';
    }

    $line = $frame['file'];

    if (isset($frame['line'])) {
        $line .= ':' . $frame['line'];
    }

    $line .= ' ';

    if (isset($frame['class'])) {
        $line .= $frame['class'] . '->';
    }

    $line .= $frame['function'];

    return $line;
}

function dump(...$values): void {
    ob_end_clean();
    print('<pre>');
    var_dump(...$values);
    print('</pre>');
    die;
}
