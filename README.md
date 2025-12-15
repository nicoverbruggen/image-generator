# Image Generator

This package is intended to be used for quickly generating placeholder images with a specific size, color and text. For more complex use cases, you may want to turn to something like [stil/gd-text](https://github.com/stil/gd-text).

## Requirements

* PHP 8.1 or higher
* GD extension
    
## Usage

Make sure you require this package in your composer.json:

    composer require nicoverbruggen/image-generator

See [the example source file](examples/saved.php) that is used to generate and save the sample images. You can generate the following examples:

![The barebones example](doc/examples/barebones.png)
![A multiline example](doc/examples/multiline.png)
![An avatar](doc/examples/avatar.png)

Please note that for testing purposes, I used Roboto Black as the TrueType font. (This font is not included in this repository.)

Here's a few examples of what you can do with this package:

### Save images to a path

```php
use NicoVerbruggen\ImageGenerator\ImageGenerator;

(new ImageGenerator())->generate(output: __DIR__ . "/image_example.png", size: '200x200');
```

### Generate `base64` encoded images inline

In addition to saving placeholder images to 

```php
use NicoVerbruggen\ImageGenerator\ImageGenerator;

$output = (new ImageGenerator())->generate(output: 'base64', size: '200x200');

echo "<img src='{$output}' alt='Placeholder image'>";
```

A useful use case may be achieved after declare your own helper, like so:

```php
function placeholder_image(string $size = '500x500'): string {
    return (new ImageGenerator())->generate(output: 'base64', size: $size);
}
```

This use case can be useful when used in combination with frameworks like Laravel or Symfony:

```bladehtml
<div>
    <h3>Item</h3>
    <img src="{{ placeholder_image('200x200') }}" alt="Placeholder">
</div>
```

### Directly output images

You can also check out [the other source file](examples/direct.php). You can point your browser directly at this file (assuming you're running a PHP server, of course) and it will directly return a file since the path is set to `null`.

### Server mode

You can also point your PHP installation's webroot to the `server` directory, and generate images via URL. The `size` parameter is used to size the placeholder images.

You can then link to the domain you're using to host these placeholders. For example, if it is `image-generator.test`:

```bladehtml
<div>
    <h3>Item</h3>
    <img src="https://image-generator.test/?size=400x400&background_color=333&text_color=FFF" alt="Placeholder">
</div>
```
## Notes

If you do not supply a TrueType font path:
* you will be limited in font size options (1 through 5)
* you will not be able to render multiline text

## Upgrade guide

### v2 to v3:

In `ImageGenerator`, `makePlaceholderImage()` has been removed. You need to replace all usages of it with `generate()`.

### v3 to v4:

In `ImageGenerator`, `generate()`'s `path` parameter has been replaced with `output`. If you use named parameters, you will need to update your usage of this method.

It works the same way, but you have more options for this parameter now, in particular `base64` is now a valid value for that parameter.

## Tests

    ./vendor/bin/phpunit tests

## Contributions

I am not planning to expand the features of this package at this time. If you've made an improvement or fixed something, you are free to send me a pull request.

## License

MIT. 

See also: [LICENSE](LICENSE).