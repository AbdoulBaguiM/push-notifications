<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '../../');
$dotenv->safeLoad();

function config(string $key, ?string $defaultValue = null)
{
    if (!isset($_ENV[$key])) {
        return $defaultValue;
    }

    return $_ENV[$key];
}