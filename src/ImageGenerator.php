<?php

namespace NicoVerbruggen\ImageGenerator;

use NicoVerbruggen\ImageGenerator\Converters\HexConverter;
use NicoVerbruggen\ImageGenerator\Helpers\ColorHelper;

class ImageGenerator
{
    /**
     * @param string $targetSize The target size for generated images.
     * @param string $textColorHex The default text color for generated images.
     * If set to null, will result in the best contrast color to the random color.
     * @param string $backgroundColorHex The default background color for generated images.
     * If set to null, will generate a random color.
     * @param string|null $fontPath Path to the font that needs to be used to render the text on the image.
     * Must be a TrueType font (.ttf) for this to work.
     * @param int $fontSize The font size to be used when a TrueType font is used.
     * Also used to calculate the line height.
     * @param int $fallbackFontSize Can be 1, 2, 3, 4, 5 for built-in fonts in latin2 encoding.
     * Higher numbers correspond to larger fonts. If the size is invalid, it will be reset to 3.
     */
    public function __construct(
        public string $targetSize = "200x200",
        public string $textColorHex = "#333",
        public string $backgroundColorHex = "#EEE",
        public string|null $fontPath = null,
        public int $fontSize = 12,
        public int $fallbackFontSize = 5
    ) {
        if ($this->fallbackFontSize < 1 || $this->fallbackFontSize > 5) {
            $this->fallbackFontSize = 3;
        }
    }

    /**
     * Generates an image; directly renders or saves a placeholder image.
     * This will always be PNG.
     *
     * @param string $text The text that should be rendered on the placeholder.
     * If left empty (""), will render the default size of the image.
     * If null, won't render any text.
     *
     * @param string|null $output
     * The output destination.
     * The path where the image needs to be stored.
     * If null will directly output the image stream to the buffer.
     * If `base64`, a `base64` representation will be returned.
     *
     * @param string|null $size The target size of the image that will be rendered.
     * For example: "100x100" is a valid size.
     * This value, if set, replaces the default value set in the renderer.
     *
     * @param string|null $bgHex The background color for the image.
     * Must be a string with a hex value. For example: "EEE" and "#EEE" are valid.
     * This value, if set, replaces the default value set in the renderer.
     *
     * @param string|null $fgHex The foreground color for the text, if applicable.
     * Must be a string with a hex value. For example: "EEE" and "#EEE" are valid.
     * This value, if set, replaces the default value set in the renderer.
     *
     * @return bool
     */
    public function generate(
        string $text = "",
        ?string $output = null,
        ?string $size = null,
        ?string $bgHex = null,
        ?string $fgHex = null
    ): bool|string
    {
        // The target size is either the one set in the class or the override
        $targetSize = empty($size) ? $this->targetSize : $size;

        // Extract the dimensions from the target size
        $dimensions = explode('x', $targetSize);

        // Generate an image resource with GD
        $imageResource = imagecreatetruecolor($dimensions[0], $dimensions[1]);

        $bgHex = $bgHex ?? $this->backgroundColorHex;
        $fgHex = $fgHex ?? $this->textColorHex;

        // Determine which background + foreground (text) color needs to be used
        $bgColor = ! empty($bgHex) ? $bgHex : ColorHelper::randomHex();
        $fgColor = ! empty($fgHex) ? $fgHex : ColorHelper::contrastColor($bgHex);

        if ($text === "") {
            $text = $targetSize;
        }

        // Allocate a color for the background and fill the image
        $allocatedBgColor = HexConverter::allocate($imageResource, $bgColor);
        imagefill($imageResource, 0, 0, $allocatedBgColor);

        // We'll need to use the foreground color later, so assign it to a variable
        $allocatedFgColor = HexConverter::allocate($imageResource, $fgColor);

        if ($this->fontPath !== null && file_exists($this->fontPath)) {
            $this->generateTrueTypeImage($size, $text, $imageResource, $allocatedFgColor);
        } else {
            $this->generateFallbackImage($text, $imageResource, $allocatedFgColor);
        }

        // Render image with name based on the target size
        if ($output === null) {
            ob_clean();
            $filename = !empty($text)
                ? preg_replace('/[^a-z0-9]/i', '_', $text) . '-' . $targetSize
                : $targetSize;
            header('Content-type: image/png');
            header('Content-Disposition: inline; filename="'. $filename .'.png"');
            echo imagepng($imageResource);
            exit;
        } else if ($output === 'base64') {
            ob_start();
            imagepng($imageResource);
            $data = ob_get_contents();
            ob_end_clean();
            return 'data:image/png;base64,' . base64_encode($data);
        }

        $path = $output;
        imagepng($imageResource, $path);
        return true;
    }

    private function generateFallbackImage(
        string $text,
        \GdImage $imageResource,
        int $allocatedFgColor
    ): void
    {
        $fontSize = $this->fallbackFontSize;
        $fontWidth = imagefontwidth($fontSize);
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
            (int)$x,
            (int)$y,
            $text,
            $allocatedFgColor,
        );
    }

    /**
     * @param int $size
     * @param string $text
     * @param \GdImage $imageResource
     * @param int $allocatedFgColor
     * @return void
     */
    private function generateTrueTypeImage(int $size, string $text, \GdImage $imageResource, int $allocatedFgColor): void
    {
        $font = $this->fontPath;
        $size = $this->fontSize;

        // Get the bounding box size
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
            (int)$x,
            (int)$y,
            $allocatedFgColor,
            $font,
            $text
        );
    }
}
