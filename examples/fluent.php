<?php

require "../vendor/autoload.php";

use NicoVerbruggen\ImageGenerator\ImageGenerator;

/*
|--------------------------------------------------------------------------
| FLUENT API
|--------------------------------------------------------------------------
| The fluent API is a simpler way to generate images. It uses the same
| ImageGenerator under the hood, but with a more readable syntax.
*/

// Generate a simple placeholder
ImageGenerator::text("200x200")
    ->width(200)
    ->backgroundColor("#EEE")
    ->foregroundColor("#333")
    ->toPng(__DIR__ . "/fluent_simple.png");

// Generate a rectangular placeholder (width only = square, or set both)
ImageGenerator::text("Banner")
    ->size(400, 100)
    ->backgroundColor("#005577")
    ->foregroundColor("#FFF")
    ->toPng(__DIR__ . "/fluent_banner.png");

// Generate with a TrueType font and custom line height
ImageGenerator::text("My\nname\nis\nBond.")
    ->width(200)
    ->font("../tests/fixtures/fonts/Readerly.ttf", 20)
    ->lineHeight(1.6)
    ->toPng(__DIR__ . "/fluent_multiline.png");

// Generate a base64 image (useful for inline HTML)
$base64 = ImageGenerator::text("NV")
    ->width(100)
    ->font("../tests/fixtures/fonts/Readerly.ttf", 48)
    ->backgroundColor("#663399")
    ->foregroundColor("#FFF")
    ->toBase64();

echo "Base64 avatar generated: " . strlen($base64) . " characters\n";
