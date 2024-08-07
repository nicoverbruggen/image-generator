<?php

require "../vendor/autoload.php";

use NicoVerbruggen\ImageGenerator\ImageGenerator;

// If you point your browser at this file, it will spew out a PNG. Enjoy.

$output = (new ImageGenerator())->generate(output: 'base64', size: '200x200');

