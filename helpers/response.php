<?php

function response(bool $success, $result)
{
    $response = [
        'success' => $success,
        'result' => $result,
    ];

    header("Content-type: application/json");
    echo json_encode($response, JSON_THROW_ON_ERROR);
    exit;
}

function forbidden(): void
{
    header('HTTP/1.0 403 Forbidden');
    die('You are forbidden!');
}