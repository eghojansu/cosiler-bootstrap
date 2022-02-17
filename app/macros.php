<?php

use Ekok\Utils\Arr;
use Ekok\Utils\Payload;
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
        } elseif (is_array($value) && ($arr = array_filter(array_unique($value)))) {
            if (Arr::indexed($arr)) {
                $str .= ' ' . $key . '="' . implode(' ', $arr) . '"';
            } else {
                $str .= Arr::reduce($arr, fn($str, Payload $el) => $str . ' ' . $key . $el->key . '="' . ((string) $el->value) . '"');
            }
        } elseif (true === $value) {
            $str .= ' ' . $key;
        } else {
            $str .= ' ' . $key . '="' . ((string) $value) . '"';
        }
    }

    return $str;
}

function value_attrs($value) {
    return !is_array($value) || Arr::indexed($value) ? compact('value') : Arr::each($value, static fn (Payload $item) => $item->key('value' === $item->key ? 'value' : 'data-' . $item->key));
}

function value_given($given, $value) {
    return is_array($given) && is_array($value) ? !!array_intersect($given, $value) : $given == $value;
}

function tag(string $name, array $attrs = null, string|array $content = null, bool $close = false): string {
    return '<' . $name . attrs($attrs) . ($close ? ' /' : '') . '>' . implode(' ', (array) $content) . ($content === null ? '' : '</' . $name . '>');
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
    return $message ? tag('div', array('class' => array('invalid-feedback d-block', $classes)), e($message)) : null;
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
        'class' => $classes,
        'id' => $attrs['id'] ?? ('input' . Str::casePascal($name)),
        'placeholder' => $attrs['placeholder'] ?? Str::caseTitle($name),
    );

    return tag('input', $sets + ($attrs ?? array()));
}

function text(
    string $name,
    string|int|float|bool|null $value = null,
    array $attrs = null,
    string|array $classes = null,
): string {
    $sets = array(
        'name' => $name,
        'class' => $classes,
        'id' => $attrs['id'] ?? ('input' . Str::casePascal($name)),
        'placeholder' => $attrs['placeholder'] ?? Str::caseTitle($name),
    );

    return tag('textarea', $sets + ($attrs ?? array()), e($value));
}

function choice(
    string $name,
    array $options,
    string|int|float|bool|null|array $value = null,
    array $attrs = null,
    string|array $classes = null,
    bool $expanded = null,
    bool $multiple = null,
): string {
    $sets = array(
        'name' => $name,
        'class' => (array) $classes,
        'placeholder' => false,
    ) + ($attrs ?? array());

    if ($expanded) {
        $sets['class'][] = 'form-check-input';

        if ($multiple) {
            return Arr::reduce($options, static function (string|null $choices, Payload $option) use ($name, $value, $sets) {
                $attrs = value_attrs($option->value);
                $attrs['id'] = 'choice' . Str::casePascal($name) . Str::casePascal(str_replace(' ', '_', $option->key));
                $attrs['checked'] = value_given($attrs['value'], $value);

                return $choices . ($choices ? PHP_EOL : '') . tag(
                    'div',
                    array('class' => 'form-check'),
                    input($name . '[]', $attrs['value'], $attrs + $sets, $sets['class'], 'checkbox') .
                    tag('label', array('class' => 'form-check-label', 'for' => $attrs['id']), $option->key),
                );
            });
        }

        return Arr::reduce($options, static function (string|null $choices, Payload $option) use ($name, $value, $sets) {
            $attrs = value_attrs($option->value);
            $attrs['id'] = 'choice' . Str::casePascal($name) . Str::casePascal(str_replace(' ', '_', $option->key));
            $attrs['checked'] = $attrs['value'] == $value;

            return $choices . ($choices ? PHP_EOL : '') . tag(
                'div',
                array('class' => 'form-check'),
                input($name, $attrs['value'], $attrs + $sets, $sets['class'], 'radio') .
                tag('label', array('class' => 'form-check-label', 'for' => $attrs['id']), $option->key),
            );
        });
    }

    $sets['id'] = $attrs['id'] ?? ('input' . Str::caseCamel($name));

    $choices = Arr::reduce($options, static function (string $choices, Payload $option) use ($value) {
        $attrs = value_attrs($option->value);
        $attrs['selected'] = value_given($attrs['value'], $value);

        return $choices . ($choices ? PHP_EOL : '') . tag('option', $attrs, $option->key);
    }, false === ($attrs['placeholder'] ?? null) ? '' : tag('option', null, '-- Select ' . Str::caseTitle($name) . ' --'));

    return tag('select', $sets, $choices);
}

function pagination_footer(array $page, string $path = null, array $query = null): string|null {
    if (0 >= $page['total']) {
        return null;
    }

    $url = static fn (int $page) => ($path ?? current_path()) . '?' . http_build_query(compact('page') + ($query ?? $_GET));
    $last = $page['current_page'] === $page['last_page'];
    $first = $page['current_page'] === 1;

    return tag(
        'div',
        array('class' => 'row mt-3'),
        array(
            tag(
                'div',
                array('class' => 'col d-flex justify-content-end'),
                tag(
                    'div',
                    array('class' => 'btn-group'),
                    array(
                        tag(
                            'a',
                            array(
                                'href' => $first ? '#' : $url($page['prev_page']),
                                'class' => array('btn btn-outline-secondary', $first ? 'disabled' : null),
                            ),
                            'Prev',
                        ),
                        tag(
                            'a',
                            array(
                                'href' => $last ? '#' : $url($page['next_page']),
                                'class' => array('btn btn-outline-secondary', $last ? 'disabled' : null),
                            ),
                            'Next',
                        ),
                    ),
                ),
            ),
        ),
    );
}

function nav(array $menu, array $attrs = null, array $options = null): string|null {
    $opt = array_merge(array(
        'end' => false,
        'dropdown' => false,
    ), $options ?? array());
    $result = array_reduce(
        $menu,
        static function (string|null $result, array $item) use ($opt) {
            $attrs = array(
                // 'class' => array('nav-item'),
                'class' => array($opt['dropdown'] ? false : 'nav-item'),
            );
            $aAttrs = array(
                'href' => $item['path'],
                // 'class' => array('nav-link'),
                'class' => array($opt['dropdown'] ? 'dropdown-item' : 'nav-link'),
            );
            $child = '';
            $text = $item['title'];

            if (isset($item['icon'])) {
                $text = tag('i', array('class' => 'bi-' . $item['icon']), '') . ' ' . $text;
            }

            if ($item['items']) {
                $aAttrs['role'] = 'button';
                $aAttrs['data-bs-toggle'] = 'dropdown';
                $aAttrs['aria-expanded'] = 'false';
                $aAttrs['class'][] = 'dropdown-toggle';
                $attrs['class'][] = 'dropdown';

                $child = nav($item['items'], array(
                    'class' => array('dropdown-menu', $opt['end'] ? 'dropdown-menu-end' : null),
                ), array(
                    'dropdown' => true,
                ));
            }

            $anchor = tag('a', $aAttrs, $text);

            return $result . tag('li', $attrs, $anchor . $child);
        },
    );
    $attrs['class'] = (array) ($attrs['class'] ?? array());
    $attrs['class'][] = $opt['dropdown'] ? 'dropdown-menu' : 'navbar-nav';

    return $result ? tag('ul', $attrs, $result) : null;
}
