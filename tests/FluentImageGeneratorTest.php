<?php

use PHPUnit\Framework\TestCase;
use NicoVerbruggen\ImageGenerator\ImageGenerator;

class FluentImageGeneratorTest extends TestCase
{
    private string $outputPath;

    protected function setUp(): void
    {
        $this->outputPath = __DIR__ . '/fluent_output.png';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->outputPath)) {
            unlink($this->outputPath);
        }
    }

    public function testToBase64(): void
    {
        $result = ImageGenerator::text("Hello")
            ->width(100)
            ->backgroundColor("#FFF")
            ->foregroundColor("#333")
            ->toBase64();

        $this->assertStringStartsWith('data:image/png;base64', $result);
    }

    public function testToPng(): void
    {
        $result = ImageGenerator::text("Hello")
            ->width(100)
            ->backgroundColor("#FFF")
            ->foregroundColor("#333")
            ->toPng($this->outputPath);

        $this->assertTrue($result);
        $this->assertEquals('image/png', mime_content_type($this->outputPath));
    }

    public function testWidthOnlyGeneratesSquare(): void
    {
        ImageGenerator::text("Square")
            ->width(150)
            ->toPng($this->outputPath);

        $imageInfo = getimagesize($this->outputPath);
        $this->assertEquals(150, $imageInfo[0]);
        $this->assertEquals(150, $imageInfo[1]);
    }

    public function testWidthAndHeight(): void
    {
        ImageGenerator::text("Rectangle")
            ->width(300)
            ->height(150)
            ->toPng($this->outputPath);

        $imageInfo = getimagesize($this->outputPath);
        $this->assertEquals(300, $imageInfo[0]);
        $this->assertEquals(150, $imageInfo[1]);
    }

    public function testSizeMethod(): void
    {
        ImageGenerator::text("Size")
            ->size(400, 200)
            ->toPng($this->outputPath);

        $imageInfo = getimagesize($this->outputPath);
        $this->assertEquals(400, $imageInfo[0]);
        $this->assertEquals(200, $imageInfo[1]);
    }

    public function testDefaultSizeIs200x200(): void
    {
        ImageGenerator::text("Default")
            ->toPng($this->outputPath);

        $imageInfo = getimagesize($this->outputPath);
        $this->assertEquals(200, $imageInfo[0]);
        $this->assertEquals(200, $imageInfo[1]);
    }

    public function testWithTrueTypeFont(): void
    {
        $fontPath = __DIR__ . '/fixtures/fonts/Readerly.ttf';

        $result = ImageGenerator::text("TTF")
            ->width(200)
            ->font($fontPath, 24)
            ->toBase64();

        $this->assertStringStartsWith('data:image/png;base64', $result);
    }

    public function testWithLineHeight(): void
    {
        $fontPath = __DIR__ . '/fixtures/fonts/Readerly.ttf';

        $default = ImageGenerator::text("A\nB\nC")
            ->width(200)
            ->backgroundColor("#EEE")
            ->foregroundColor("#333")
            ->font($fontPath, 20)
            ->toBase64();

        $spaced = ImageGenerator::text("A\nB\nC")
            ->width(200)
            ->backgroundColor("#EEE")
            ->foregroundColor("#333")
            ->font($fontPath, 20)
            ->lineHeight(3.0)
            ->toBase64();

        $this->assertNotEquals($default, $spaced, "Different line heights should produce different images.");
    }

    public function testProducesSameResultAsImageGenerator(): void
    {
        $fluent = ImageGenerator::text("Hello")
            ->size(200, 100)
            ->backgroundColor("#EEE")
            ->foregroundColor("#333")
            ->toBase64();

        $classic = (new ImageGenerator(
            targetSize: "200x100",
            textColorHex: "#333",
            backgroundColorHex: "#EEE",
        ))->generate(text: "Hello", output: 'base64');

        $this->assertEquals($classic, $fluent, "Fluent API should produce the same image as the classic API.");
    }
}
