<?php

require "../vendor/autoload.php";

use NicoVerbruggen\ImageGenerator\ImageGenerator;

// If you point your browser at this file, it will spew out a PNG. Enjoy.

$generator = new ImageGenerator(
    // Decide on a target size for your image
    targetSize: "200x200",
    // Fun fact: if you set null for these, you'll get a random color for each generated placeholder!
    // You can also specify a specific hex color. ("#EEE" or "#EEEEEE" are both accepted)
    textColorHex: "#333",
    backgroundColorHex: "#AFB"
);

$generator->generate();
