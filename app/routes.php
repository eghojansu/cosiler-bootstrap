<?php

use Ekok\Cosiler\Route;
use Ekok\Cosiler\Http\Response;

Route\globals_add('fun', $fun = storage());
ob_start();
$result = Route\files(__DIR__ . '/../routes');
$output = ob_get_clean();

if (is_array($result) || $result instanceof JsonSerializable) {
    Response\json($result);
} elseif (Route\did_match()) {
    Response\start(200);
    load('main', compact('output', 'fun'));
} else {
    not_found();
}
