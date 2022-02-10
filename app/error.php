<?php

use Ekok\Cosiler\Http;
use Ekok\Cosiler\Http\Request;
use Ekok\Cosiler\Http\Response;
use Ekok\Cosiler\Http\HttpException;
use Ekok\Validation\ValidationException;

try {
    handleError($error);
} catch (Throwable $e) {
    handleError($e);
}

function handleError(Throwable $error) {
    $dev = env_is('dev');
    $code = $error instanceof HttpException ? $error->statusCode : 500;
    $data = compact('dev', 'code') + array(
        'text' => Http\status($code),
        'message' => $error->getMessage(),
    );

    if ($dev) {
        $data['trace'] = array_filter(array_map('format_trace_frame', $error->getTrace()));
    }

    if ($error instanceof ValidationException) {
        $data['errors'] = $error->result->getErrors();
    }

    if (Request\wants_json()) {
        Response\json($data, $data['code']);
    } elseif ($error instanceof ValidationException) {
        error_commit($data['message'], $error->result->getErrors());
        data_commit($error->result->getData());
        back();
    } else {
        Response\start($code);
        load('error', $data);
    }
}
