<?php

use PHPUnit\Framework\TestCase;
use NicoVerbruggen\ImageGenerator\Helpers\ColorHelper;

class ColorHelperTest extends TestCase
{
    public function testRandomHexReturnsValidFormat(): void
    {
        $hex = ColorHelper::randomHex();

        $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $hex);
    }

    public function testRandomHexReturnsHashPrefix(): void
    {
        $hex = ColorHelper::randomHex();

        $this->assertStringStartsWith('#', $hex);
    }

    public function testRandomHexReturns7CharString(): void
    {
        $hex = ColorHelper::randomHex();

        $this->assertEquals(7, strlen($hex));
    }

    public function testRandomHexProducesDifferentResults(): void
    {
        $colors = [];
        for ($i = 0; $i < 10; $i++) {
            $colors[] = ColorHelper::randomHex();
        }

        // With 10 random colors, we should have at least 2 unique colors
        $uniqueColors = array_unique($colors);
        $this->assertGreaterThan(1, count($uniqueColors));
    }

    public function testContrastColorReturnsBlackForLightBackground(): void
    {
        // White background should return black text
        $this->assertEquals('#000', ColorHelper::contrastColor('#FFFFFF'));
        $this->assertEquals('#000', ColorHelper::contrastColor('#FFF'));
        $this->assertEquals('#000', ColorHelper::contrastColor('FFFFFF'));

        // Light yellow (high luminosity due to green + red channels)
        $this->assertEquals('#000', ColorHelper::contrastColor('#FFFF00'));

        // Very light gray (high enough luminosity for black text)
        $this->assertEquals('#000', ColorHelper::contrastColor('#EEEEEE'));
    }

    public function testContrastColorReturnsWhiteForDarkBackground(): void
    {
        // Black background should return white text
        $this->assertEquals('#FFF', ColorHelper::contrastColor('#000000'));
        $this->assertEquals('#FFF', ColorHelper::contrastColor('#000'));
        $this->assertEquals('#FFF', ColorHelper::contrastColor('000000'));

        // Dark blue
        $this->assertEquals('#FFF', ColorHelper::contrastColor('#000080'));

        // Dark gray
        $this->assertEquals('#FFF', ColorHelper::contrastColor('#333333'));
    }

    public function testContrastColorWithNullReturnsWhite(): void
    {
        // Null defaults to black background, which needs white text
        $this->assertEquals('#FFF', ColorHelper::contrastColor(null));
    }

    public function testContrastColorWithEmptyStringReturnsWhite(): void
    {
        // Empty string defaults to black background, which needs white text
        $this->assertEquals('#FFF', ColorHelper::contrastColor(''));
    }

    public function testContrastColorWithMidToneColors(): void
    {
        // Pure red (medium luminosity)
        $result = ColorHelper::contrastColor('#FF0000');
        $this->assertContains($result, ['#000', '#FFF']);

        // Pure green (high luminosity due to green channel weight)
        $result = ColorHelper::contrastColor('#00FF00');
        $this->assertEquals('#000', $result);

        // Pure blue (low luminosity)
        $result = ColorHelper::contrastColor('#0000FF');
        $this->assertEquals('#FFF', $result);
    }

    public function testContrastColorHandlesHashPrefix(): void
    {
        $withHash = ColorHelper::contrastColor('#FFFFFF');
        $withoutHash = ColorHelper::contrastColor('FFFFFF');

        $this->assertEquals($withHash, $withoutHash);
    }
}
