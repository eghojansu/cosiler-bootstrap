<?php

use Ekok\Utils\Arr;
use Ekok\Utils\Str;

function e(string|int|float|bool|null $str): string|null {
    return $str ? htmlspecialchars($str) : null;
}

function attrs(array $attrs = null): string {
    $str = '';

    foreach ($attrs ?? array() as $key => $value) {
        if (null === $value || false === $value || '' === $value) {
            continue;
        }

        if (is_numeric($key)) {
            $str .= ' ' . ((string) $value);
        } elseif (is_array($value) && ($arr = array_filter($value))) {
            if (is_numeric(implode('', array_keys($arr)))) {
                $str .= ' ' . $key . '="' . implode(' ', $value) . '"';
            } else {
                $str .= Arr::reduce($arr, fn($str, $el) => $str . ' ' . $key . $el->key . '="' . ((string) $el->value) . '"');
            }
        } elseif (true === $value) {
            $str .= ' ' . $key;
        } else {
            $str .= ' ' . $key . '="' . ((string) $value) . '"';
        }
    }

    return $str;
}

function tag(string $name, array $attrs = null, string $content = null, bool $close = false): string {
    return '<' . $name . attrs($attrs) . ($close ? ' /' : '') . '>' . $content . ($content === null ? '' : '</' . $name . '>');
}

function alert(string|null $message, string $type = null, bool $dismissible = true): string|null {
    if (!$message) {
        return null;
    }

    $class = 'alert alert-' . ($type ?? 'danger');
    $extra = null;

    if ($dismissible) {
        $class .= ' alert-dismissible fade show';
        $extra = ' ' . tag('button', array(
            'type' => 'button',
            'class' => 'btn-close',
            'data-bs-dismiss' => 'alert',
            'aria-label' => 'Close',
        ));
    }

    return tag(
        'div',
        compact('class') + array('role' => 'alert'),
        $message . $extra,
    );
}

function feedback(string|null $message, string|array $classes = null): string|null {
    return $message ? tag('div', array('class' => array('invalid-feedback', $classes)), e($message)) : null;
}

function input(
    string $name,
    string|int|float|bool|null $value = null,
    array $attrs = null,
    string|array $classes = null,
    string $type = null,
): string {
    $sets = array(
        'name' => $name,
        'type' => $type ?? 'text',
        'value' => e($value),
        'id' => 'input' . Str::casePascal($name),
        'class' => $classes,
        'placeholder' => Str::caseTitle($name),
    );

    return tag('input', $sets + ($attrs ?? array()));
}
