<?php

use PHPUnit\Framework\TestCase;
use NicoVerbruggen\ImageGenerator\ImageGenerator;

class ImageGeneratorTest extends TestCase
{
    private string $outputPath;

    protected function setUp(): void
    {
        $this->outputPath = __DIR__ . '/output.png';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->outputPath)) {
            unlink($this->outputPath);
        }
    }

    public function testCanGenerateToFile(): void
    {
        $generator = new ImageGenerator(
            targetSize: "200x200",
            textColorHex: "#333",
            backgroundColorHex: "#AFB",
        );

        $generator->generate(output: $this->outputPath);

        $this->assertEquals('image/png', mime_content_type($this->outputPath));
    }

    public function testCanGenerateToBase64Image(): void
    {
        $generator = new ImageGenerator(
            targetSize: "200x200",
            textColorHex: "#333",
            backgroundColorHex: "#AFB",
        );

        $output = $generator->generate(output: 'base64');

        $this->assertStringStartsWith('data:image/png;base64', $output);
    }

    public function testGeneratesCorrectDimensions(): void
    {
        $generator = new ImageGenerator(targetSize: "300x150");

        $generator->generate(output: $this->outputPath);

        $imageInfo = getimagesize($this->outputPath);
        $this->assertEquals(300, $imageInfo[0]);
        $this->assertEquals(150, $imageInfo[1]);
    }

    public function testCanOverrideSizeInGenerate(): void
    {
        $generator = new ImageGenerator(targetSize: "200x200");

        $generator->generate(output: $this->outputPath, size: "400x300");

        $imageInfo = getimagesize($this->outputPath);
        $this->assertEquals(400, $imageInfo[0]);
        $this->assertEquals(300, $imageInfo[1]);
    }

    public function testCanOverrideColorsInGenerate(): void
    {
        $generator = new ImageGenerator(
            targetSize: "100x100",
            textColorHex: "#000",
            backgroundColorHex: "#FFF",
        );

        // Should not throw even with overridden colors
        $result = $generator->generate(
            output: $this->outputPath,
            bgHex: "#FF0000",
            fgHex: "#00FF00"
        );

        $this->assertTrue($result);
        $this->assertFileExists($this->outputPath);
    }

    public function testTextDefaultsToSizeWhenEmpty(): void
    {
        $generator = new ImageGenerator(targetSize: "100x100");

        // Empty string should render the size as text
        $result = $generator->generate(text: "", output: 'base64');

        $this->assertIsString($result);
        $this->assertStringStartsWith('data:image/png;base64', $result);
    }

    public function testFallbackFontSizeResetToDefaultWhenInvalid(): void
    {
        $generator = new ImageGenerator(
            targetSize: "100x100",
            fallbackFontSize: 10 // Invalid: must be 1-5
        );

        // Should reset to 3 and work fine
        $result = $generator->generate(output: 'base64');

        $this->assertIsString($result);
    }

    public function testFallbackFontSizeZeroResetsToDefault(): void
    {
        $generator = new ImageGenerator(
            targetSize: "100x100",
            fallbackFontSize: 0
        );

        $result = $generator->generate(output: 'base64');

        $this->assertIsString($result);
    }

    public function testFallbackFontSizeNegativeResetsToDefault(): void
    {
        $generator = new ImageGenerator(
            targetSize: "100x100",
            fallbackFontSize: -1
        );

        $result = $generator->generate(output: 'base64');

        $this->assertIsString($result);
    }

    public function testValidFallbackFontSizes(): void
    {
        for ($size = 1; $size <= 5; $size++) {
            $generator = new ImageGenerator(
                targetSize: "100x100",
                fallbackFontSize: $size
            );

            $result = $generator->generate(output: 'base64');

            $this->assertIsString($result, "Failed for font size {$size}");
        }
    }

    public function testBase64OutputIsValidPngData(): void
    {
        $generator = new ImageGenerator(targetSize: "50x50");

        $output = $generator->generate(output: 'base64');

        // Extract the base64 part
        $base64Data = str_replace('data:image/png;base64,', '', $output);
        $decodedData = base64_decode($base64Data, true);

        $this->assertNotFalse($decodedData);

        // Verify it's a valid PNG by checking magic bytes
        $this->assertEquals("\x89PNG", substr($decodedData, 0, 4));
    }

    public function testGenerateReturnsTrue(): void
    {
        $generator = new ImageGenerator(targetSize: "50x50");

        $result = $generator->generate(output: $this->outputPath);

        $this->assertTrue($result);
    }

    // Validation tests

    public function testThrowsExceptionForInvalidSizeFormat(): void
    {
        $generator = new ImageGenerator(targetSize: "200x200");

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid size format");

        $generator->generate(output: 'base64', size: "notasize");
    }

    public function testThrowsExceptionForSizeMissingX(): void
    {
        $generator = new ImageGenerator(targetSize: "200x200");

        $this->expectException(\InvalidArgumentException::class);

        $generator->generate(output: 'base64', size: "200200");
    }

    public function testThrowsExceptionForSizeWithNonNumericValues(): void
    {
        $generator = new ImageGenerator(targetSize: "200x200");

        $this->expectException(\InvalidArgumentException::class);

        $generator->generate(output: 'base64', size: "abcxdef");
    }

    public function testThrowsExceptionForZeroDimensions(): void
    {
        $generator = new ImageGenerator(targetSize: "200x200");

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Dimensions must be at least 1x1");

        $generator->generate(output: 'base64', size: "0x100");
    }

    public function testThrowsExceptionForExcessiveDimensions(): void
    {
        $generator = new ImageGenerator(targetSize: "200x200");

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("exceed maximum allowed size");

        $generator->generate(output: 'base64', size: "10000x100");
    }

    public function testThrowsExceptionForInvalidBackgroundColor(): void
    {
        $generator = new ImageGenerator(targetSize: "100x100");

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid background color");

        $generator->generate(output: 'base64', bgHex: "notacolor");
    }

    public function testThrowsExceptionForInvalidForegroundColor(): void
    {
        $generator = new ImageGenerator(targetSize: "100x100");

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid foreground color");

        $generator->generate(output: 'base64', fgHex: "XY");
    }

    public function testAcceptsVariousValidColorFormats(): void
    {
        $generator = new ImageGenerator(targetSize: "50x50");

        // 3-char hex
        $this->assertIsString($generator->generate(output: 'base64', bgHex: "#FFF"));

        // 6-char hex
        $this->assertIsString($generator->generate(output: 'base64', bgHex: "#FFFFFF"));

        // Without hash
        $this->assertIsString($generator->generate(output: 'base64', bgHex: "ABC"));

        // Lowercase
        $this->assertIsString($generator->generate(output: 'base64', bgHex: "#aabbcc"));
    }

    public function testMinimumDimensions(): void
    {
        $generator = new ImageGenerator(targetSize: "1x1");

        $generator->generate(output: $this->outputPath);

        $imageInfo = getimagesize($this->outputPath);
        $this->assertEquals(1, $imageInfo[0]);
        $this->assertEquals(1, $imageInfo[1]);
    }

    public function testMaximumAllowedDimensions(): void
    {
        $generator = new ImageGenerator(targetSize: "5000x5000");

        $result = $generator->generate(output: 'base64');

        $this->assertIsString($result);
    }

    public function testCustomTextRendering(): void
    {
        $generator = new ImageGenerator(targetSize: "200x100");

        $result = $generator->generate(text: "Hello World", output: 'base64');

        $this->assertIsString($result);
    }

    public function testSpecialCharactersInText(): void
    {
        $generator = new ImageGenerator(targetSize: "200x100");

        // Should handle special characters without crashing
        $result = $generator->generate(text: "Test@#$%", output: 'base64');

        $this->assertIsString($result);
    }

    public function testLongTextString(): void
    {
        $generator = new ImageGenerator(targetSize: "500x100");

        $longText = str_repeat("A", 100);
        $result = $generator->generate(text: $longText, output: 'base64');

        $this->assertIsString($result);
    }

    public function testUnicodeText(): void
    {
        $generator = new ImageGenerator(targetSize: "200x100");

        // Built-in fonts may not render all unicode, but shouldn't crash
        $result = $generator->generate(text: "Héllo", output: 'base64');

        $this->assertIsString($result);
    }

    public function testConstructorDefaultValues(): void
    {
        $generator = new ImageGenerator();

        $this->assertEquals("200x200", $generator->targetSize);
        $this->assertEquals("#333", $generator->textColorHex);
        $this->assertEquals("#EEE", $generator->backgroundColorHex);
        $this->assertNull($generator->fontPath);
        $this->assertEquals(12, $generator->fontSize);
        $this->assertEquals(5, $generator->fallbackFontSize);
    }

    public function testInvalidSizeInConstructor(): void
    {
        $generator = new ImageGenerator(targetSize: "invalid");

        $this->expectException(\InvalidArgumentException::class);

        $generator->generate(output: 'base64');
    }
}