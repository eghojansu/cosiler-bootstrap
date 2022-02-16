<?php

use Ekok\Cosiler\Route;
use Ekok\Cosiler\Http\Response;

empty($_GET['dump']) || dump(storage()->menu);
record('visit', false);

Route\globals_add('fun', $fun = storage());
ob_start();
$result = Route\files(__DIR__ . '/../routes');
$output = ob_get_clean();

if (is_array($result) || $result instanceof JsonSerializable) {
    Response\json($result);
} elseif (Route\did_match()) {
    if ($layout = layout_used()) {
        Response\start(200);
        load($layout, compact('output', 'fun'));
    } else {
        Response\html($output);
    }
} else {
    not_found();
}
