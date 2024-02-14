<?php

$route = $_SERVER['REQUEST_URI'];

if ($route === '/' || $route === '') {
    return include(__DIR__ . '/_Server/Home.php');
}

if (str_starts_with($route, '/client-error-response')) {
    $responseCodes = [400, 401, 404, 405, 410];

    http_response_code($responseCodes[rand(0, 4)]);

    return;
}

if (str_starts_with($route, '/server-error-response')) {
    $responseCodes = [500, 502, 505, 521];

    http_response_code($responseCodes[rand(0, 3)]);

    return;
}
