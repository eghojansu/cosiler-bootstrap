<?php

use Ekok\Utils\Arr;

function e(string|int|float|bool|null $str) {
    return $str ? htmlspecialchars($str) : null;
}

function attrs(array $attrs = null) {
    $str = '';

    foreach ($attrs ?? array() as $key => $value) {
        if (null === $value || false === $value || '' === $value) {
            continue;
        }

        if (is_numeric($key)) {
            $str .= ' ' . ((string) $value);
        } elseif (is_array($value)) {
            if (is_numeric(implode('', array_keys($value)))) {
                $str .= ' ' . $key . '="' . implode(' ', $value) . '"';
            } else {
                $str .= Arr::reduce($value, fn($str, $el) => $str . ' ' . $key . $el->key . '="' . ((string) $el->value) . '"');
            }
        } elseif (true === $value) {
            $str .= ' ' . $key;
        } else {
            $str .= ' ' . $key . '="' . ((string) $value) . '"';
        }
    }

    return $str;
}

function tag(string $name, array $attrs = null, string $content = null, bool $close = false) {
    return '<' . $name . attrs($attrs) . ($close ? ' /' : '') . '>' . $content . ($content ? '</' . $name . '>' : '');
}

function alert(string|null $message, string $type = null, bool $dismissible = true) {
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
