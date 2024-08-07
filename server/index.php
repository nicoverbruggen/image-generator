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
    $textColor = '#' . $_GET['text_color'];
}

if (array_key_exists('background_color', $_GET)) {
    $backgroundColor = '#' . $_GET['background_color'];
}

return (new ImageGenerator(
    targetSize: $size,
    textColorHex: $textColor,
    backgroundColorHex: $backgroundColor,
))->generate();
