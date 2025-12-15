<?php

require "../vendor/autoload.php";

use NicoVerbruggen\ImageGenerator\ImageGenerator;

$size = '200x200';
$textColor = "#333";
$backgroundColor = "#EEE";

if (array_key_exists('size', $_GET)) {
    $size = $_GET['size'];
}

if (array_key_exists('text_color', $_GET)) {
    // Sanitize: only allow valid hex characters
    $color = preg_replace('/[^a-fA-F0-9]/', '', $_GET['text_color']);
    if ($color !== '') {
        $textColor = '#' . $color;
    }
}

if (array_key_exists('background_color', $_GET)) {
    // Sanitize: only allow valid hex characters
    $color = preg_replace('/[^a-fA-F0-9]/', '', $_GET['background_color']);
    if ($color !== '') {
        $backgroundColor = '#' . $color;
    }
}

try {
    return (new ImageGenerator(
        targetSize: $size,
        textColorHex: $textColor,
        backgroundColorHex: $backgroundColor,
    ))->generate();
} catch (\InvalidArgumentException $e) {
    http_response_code(400);
    header('Content-Type: text/plain');
    echo 'Bad Request: ' . $e->getMessage();
    exit;
}
