<?php

require "../vendor/autoload.php";

use NicoVerbruggen\ImageGenerator\RequestHandler;

try {
    $handler = new RequestHandler($_GET);
    return $handler->createGenerator()->generate();
} catch (\InvalidArgumentException $e) {
    http_response_code(400);
    header('Content-Type: text/plain');
    echo 'Bad Request: ' . $e->getMessage();
    exit;
}
