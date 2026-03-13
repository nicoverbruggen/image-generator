# Classic API

The classic API uses the `ImageGenerator` class directly. This API is still fully supported.

## Save images to a path

```php
use NicoVerbruggen\ImageGenerator\ImageGenerator;

(new ImageGenerator())->generate(output: __DIR__ . "/image_example.png", size: '200x200');
```

## Generate `base64` encoded images inline

```php
use NicoVerbruggen\ImageGenerator\ImageGenerator;

$output = (new ImageGenerator())->generate(output: 'base64', size: '200x200');

echo "<img src='{$output}' alt='Placeholder image'>";
```

You can also declare your own helper:

```php
function placeholder_image(string $size = '500x500'): string {
    return (new ImageGenerator())->generate(output: 'base64', size: $size);
}
```

This can be useful in combination with frameworks like Laravel or Symfony:

```bladehtml
<div>
    <h3>Item</h3>
    <img src="{{ placeholder_image('200x200') }}" alt="Placeholder">
</div>
```

## Directly output images

You can point your browser directly at a PHP file and have it return a PNG. See [examples/direct.php](../examples/direct.php) for an example. This works by setting `output` to `null`.

## Advanced usage

The constructor lets you configure defaults for all generated images:

```php
$generator = new ImageGenerator(
    targetSize: "200x200",
    textColorHex: null,          // null = auto-contrast based on background
    backgroundColorHex: null,    // null = random color
    fontPath: "/path/to/font.ttf",
    fontSize: 20,
    lineHeight: 1.6,
);

$generator->generate(
    text: "My\nname\nis\nBond.",
    output: __DIR__ . "/image.png"
);
```

You can override colors and size per call:

```php
$generator->generate(
    text: "Hello",
    output: __DIR__ . "/image.png",
    size: "400x300",
    bgHex: "#FF0000",
    fgHex: "#FFFFFF"
);
```
