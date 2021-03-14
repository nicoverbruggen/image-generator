<?php


namespace NicoVerbruggen\ImageGenerator;

use NicoVerbruggen\ImageGenerator\Converters\HexConverter;
use NicoVerbruggen\ImageGenerator\Helpers\ColorHelper;

class ImageGenerator
{
    /**
     * @param string $targetSize: The target size for generated images.
     * @param string $textColorHex: The default text color for generated images. If set to null, will result in the best contrast color to the random color.
     * @param string $backgroundColorHex: The default background color for generated images. If set to null, will generate a random color.
     * @param null $fontPath: Path to the font that needs to be used to render the text on the image. Must be a TrueType font (.ttf) for this to work.
     * @param int $fontSize: The font size to be used when a TrueType font is used. Also used to calculate the line height.
     * @param int $fallbackFontSize: Can be 1, 2, 3, 4, 5 for built-in fonts in latin2 encoding (where higher numbers corresponding to larger fonts).
     */
    public function __construct(
        public $targetSize = "200x200",
        public $textColorHex = "#333",
        public $backgroundColorHex = "#EEE",
        public $fontPath = null,
        public $fontSize = 12,
        public $fallbackFontSize = 5
    ) {}

    /**
     * Generates an image; directly renders or saves a placeholder image.
     * This will always be PNG.
     *
     * @param string $text: The text that should be rendered on the placeholder.
     * If left empty (""), will render the default size of the image.
     * If null, won't render any text.
     *
     * @param null|string $path: The path where the image needs to be stored.
     * If null, will directly output the image.
     *
     * @param null|string $size: The target size of the image that will be rendered.
     * For example: "100x100" is a valid size.
     * This value, if set, replaces the default value set in the renderer.
     *
     * @param null $bgHex: The background color for the image.
     * Must be a string with a hex value. For example: "EEE" and "#EEE" are valid.
     * This value, if set, replaces the default value set in the renderer.
     *
     * @param null $fgHex: The foreground color for the text, if applicable.
     * Must be a string with a hex value. For example: "EEE" and "#EEE" are valid.
     * This value, if set, replaces the default value set in the renderer.
     *
     * @return bool
     */
    public function generate($text = "", $path = null, $size = null, $bgHex = null, $fgHex = null): bool
    {
        // The target size is either the one set in the class or the override
        $targetSize = empty($size) ? $this->targetSize : $size;

        // Extract the dimensions from the target size
        $dimensions = explode('x', $targetSize);

        // Generate an image resource with GD
        $imageResource = imagecreate($dimensions[0], $dimensions[1]);

        if ($bgHex == null) {
            $bgHex = $this->backgroundColorHex;
        }
        if ($fgHex == null) {
            $fgHex = $this->textColorHex;
        }

        $randomColor = ColorHelper::randomHex();

        // Determine which background + foreground (text) color needs to be used
        $bgColor = ! empty($bgHex) ? $bgHex : $randomColor;
        $fgColor = ! empty($fgHex) ? $fgHex : ColorHelper::contrastColor($bgHex);

        if ($text == "") {
            $text = $targetSize;
        }

        // Merely allocating the color is enough for the background
        HexConverter::allocate($imageResource, $bgColor);

        // We'll need to use the foreground color later, so assign it to a variable
        $allocatedFgColor = HexConverter::allocate($imageResource, $fgColor);

        if ($this->fontPath !== null && file_exists($this->fontPath)) {
            // Use the TrueType font that was referenced.
            // Generate text
            $font = $this->fontPath;
            $size = $this->fontSize;

            // Get Bounding Box Size
            $textBox = imagettfbbox($size, 0, $font, $text);

            // Find the outer X and Y values (min and max) and use them to calculate
            // just how wide and high the text box is!
            $xMax = max([$textBox[0], $textBox[2], $textBox[4], $textBox[6]]);
            $xMin = min([$textBox[0], $textBox[2], $textBox[4], $textBox[6]]);
            $textWidth = abs($xMax) - abs($xMin);

            $yMax = max([$textBox[1], $textBox[3], $textBox[5], $textBox[7]]);
            $yMin = min([$textBox[1], $textBox[3], $textBox[5], $textBox[7]]);
            $textHeight = abs($yMax) - abs($yMin);

            // Calculate coordinates of the text
            $x = ((imagesx($imageResource) / 2) - ($textWidth / 2));
            $y = ((imagesy($imageResource) / 2) - ($textHeight / 2));

            imagettftext(
                $imageResource,
                $size,
                0,
                $x,
                $y,
                $allocatedFgColor,
                $font,
                $text
            );
        } else {
            // The fallback font will be used!
            // Determine the size of the font and the expected size of the text that will be rendered.
            $fontSize = $this->fallbackFontSize;
            $fontWidth  = imagefontwidth($fontSize);
            $fontHeight = imagefontwidth($fontSize);
            $length = strlen($text);
            $textWidth = $length * $fontWidth;

            // Center the text in the image
            $x = (imagesx($imageResource) - $textWidth) / 2;
            $y = (imagesy($imageResource) - $fontHeight) / 2;

            // Adds the plain text string to the image
            imagestring(
                $imageResource,
                $fontSize,
                $x,
                $y,
                $text,
                $allocatedFgColor
            );
        }

        // Render image
        if ($path == null) {
            header('Content-type: image/png');
            echo imagepng($imageResource, null);
            exit;
        } else {
            imagepng($imageResource, $path);
            return true;
        }
    }

    /**
     * @deprecated: Use `generate` instead.
     * @return bool
     */
    public function makePlaceholderImage($text = "", $path = null, $size = null, $bgHex = null, $fgHex = null): bool
    {
        return $this->makePlaceholderImage($text, $path, $size, $bgHex, $fgHex);
    }
}
