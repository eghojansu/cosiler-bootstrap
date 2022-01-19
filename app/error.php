<?php

use Ekok\Cosiler\Http;
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
        errorCommit($data['message'], $error->result->getErrors());
        dataCommit($error->result->getData());
        back();
    }

    Response\start($code);
    load('error', $data);
}
