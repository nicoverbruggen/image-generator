<?php

require "../vendor/autoload.php";

use NicoVerbruggen\ImageGenerator\ImageGenerator;

// If you point your browser at this file, it will spew out a PNG. Enjoy.

$output = ImageGenerator::text("200x200")
    ->width(200)
    ->toBase64();
