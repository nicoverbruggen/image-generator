# Image Generator

This package is intended to be used for quickly generating placeholder images with a specific size, color and text. For more complex use cases, you may want to turn to something like [stil/gd-text](https://github.com/stil/gd-text).

## Requirements

* PHP 8.0 or higher
* GD extension
    
## Usage

Make sure you require this package in your composer.json:

    composer require nicoverbruggen/image-generator

See [the example source file](examples/saved.php) that is used to generate and save the sample images. You can generate the following examples:

![The barebones example](doc/examples/barebones.png)
![A multiline example](doc/examples/multiline.png)
![An avatar](doc/examples/avatar.png)

Please note that for testing purposes, I used Roboto Black as the TrueType font. (This font is not included in this repository.)

You can also check out [the other source file](examples/direct.php). You can point your browser directly at this file (assuming you're running a PHP server, of course) and it will directly return a file since the path is set to `null`.

## Notes

If you do not supply a TrueType font path: 
* you will be limited in font size options (1 through 5)
*  you will not be able to render multiline text

## Tests

    ./vendor/bin/phpunit tests

## Contributions

I am not planning to expand the features of this package at this time. If you've made an improvement or fixed something, you are free to send me a pull request.

## License

MIT. 

See also: [LICENSE](LICENSE).