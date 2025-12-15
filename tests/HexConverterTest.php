<?php

use PHPUnit\Framework\TestCase;
use NicoVerbruggen\ImageGenerator\Converters\HexConverter;

class HexConverterTest extends TestCase
{
    public function testToRgbArrayWith6CharHex(): void
    {
        $result = HexConverter::toRgbArray('FF5500');

        $this->assertIsArray($result);
        $this->assertEquals(255, $result['r']);
        $this->assertEquals(85, $result['g']);
        $this->assertEquals(0, $result['b']);
    }

    public function testToRgbArrayWith6CharHexWithHash(): void
    {
        $result = HexConverter::toRgbArray('#FF5500');

        $this->assertIsArray($result);
        $this->assertEquals(255, $result['r']);
        $this->assertEquals(85, $result['g']);
        $this->assertEquals(0, $result['b']);
    }

    public function testToRgbArrayWith3CharHex(): void
    {
        $result = HexConverter::toRgbArray('F50');

        $this->assertIsArray($result);
        $this->assertEquals(255, $result['r']);
        $this->assertEquals(85, $result['g']);
        $this->assertEquals(0, $result['b']);
    }

    public function testToRgbArrayWith3CharHexWithHash(): void
    {
        $result = HexConverter::toRgbArray('#F50');

        $this->assertIsArray($result);
        $this->assertEquals(255, $result['r']);
        $this->assertEquals(85, $result['g']);
        $this->assertEquals(0, $result['b']);
    }

    public function testToRgbArrayWithBlack(): void
    {
        $result = HexConverter::toRgbArray('#000000');

        $this->assertIsArray($result);
        $this->assertEquals(0, $result['r']);
        $this->assertEquals(0, $result['g']);
        $this->assertEquals(0, $result['b']);
    }

    public function testToRgbArrayWithWhite(): void
    {
        $result = HexConverter::toRgbArray('#FFFFFF');

        $this->assertIsArray($result);
        $this->assertEquals(255, $result['r']);
        $this->assertEquals(255, $result['g']);
        $this->assertEquals(255, $result['b']);
    }

    public function testToRgbArrayWithLowercase(): void
    {
        $result = HexConverter::toRgbArray('#aabbcc');

        $this->assertIsArray($result);
        $this->assertEquals(170, $result['r']);
        $this->assertEquals(187, $result['g']);
        $this->assertEquals(204, $result['b']);
    }

    public function testToRgbArrayWithInvalidLength(): void
    {
        $this->assertFalse(HexConverter::toRgbArray('FF55'));
        $this->assertFalse(HexConverter::toRgbArray('FF550'));
        $this->assertFalse(HexConverter::toRgbArray('FF55001'));
        $this->assertFalse(HexConverter::toRgbArray(''));
    }

    public function testToRgbArrayStripsInvalidCharacters(): void
    {
        // "GHI123" -> strips GHI -> "123" (3 chars) -> expands to 112233
        $result = HexConverter::toRgbArray('GHI123');

        $this->assertIsArray($result);
        $this->assertEquals(17, $result['r']);
        $this->assertEquals(34, $result['g']);
        $this->assertEquals(51, $result['b']);
    }

    public function testAllocateReturnsInt(): void
    {
        $image = imagecreatetruecolor(10, 10);
        $result = HexConverter::allocate($image, '#FF0000');

        $this->assertIsInt($result);
        unset($image);
    }

    public function testAllocateWithDifferentColors(): void
    {
        $image = imagecreatetruecolor(10, 10);

        $red = HexConverter::allocate($image, '#FF0000');
        $green = HexConverter::allocate($image, '#00FF00');
        $blue = HexConverter::allocate($image, '#0000FF');

        // Each color should produce a different allocation
        $this->assertNotEquals($red, $green);
        $this->assertNotEquals($green, $blue);
        $this->assertNotEquals($red, $blue);

        unset($image);
    }
}
