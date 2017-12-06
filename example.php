<?php

require "vendor/autoload.php";

use NicoVerbruggen\ImageGenerator\ImageGenerator;

/*
|--------------------------------------------------------------------------
| BAREBONES
|--------------------------------------------------------------------------
| We'll just use the generator to generate a default, simple placeholder
| that just shows the image size. In order to automatically set the image
| size, we'll keep the text argument empty.
*/

// Let's use a barebones generator first
$generator = new ImageGenerator();
$generator->makePlaceholderImage(
    "",
    __DIR__ . "/image_example_barebones.png"
);

/*
|--------------------------------------------------------------------------
| ADVANCED
|--------------------------------------------------------------------------
| We can play with fonts, multiline text and such. Just make sure you
| add the correct path to the font in the configuration! The font does
| NOT ship with this repository, so please try using one of your own fonts!
*/

// Create a new instance of ImageGenerator
$generator = new ImageGenerator([
    // Decide on a target size for your image
    'targetSize' => "200x200",
    // Fun fact: if you set null for these, you'll get a random color for each generated placeholder!
    // You can also specify a specific hex color. ("#EEE" or "#EEEEEE" are both accepted)
    'textColorHex' => null,
    'backgroundColorHex' => null,
    // Let's point to a font. If it can't be found, it'll use a fallback (built-in to GD)
    'pathToFont' => __DIR__ . "/Roboto-Black.ttf",
    'fontSize' => 20
]);

// We'll do a multiline message here
$generator->makePlaceholderImage(
    "My\nname\nis\nBond.", // The text that will be added to the image
    __DIR__ . "/image_example.png" // The path where the image will be saved
);

// Generate a makePlaceholderImage image, for example for an avatar with initials
// We'll increase the font size first!
$generator->fontSize = 90;
$generator->makePlaceholderImage(
    "NV",
    __DIR__ . "/image_example_avatar.png"
);