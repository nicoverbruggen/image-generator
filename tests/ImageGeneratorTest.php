<?php

use PHPUnit\Framework\TestCase;
use NicoVerbruggen\ImageGenerator\ImageGenerator;

class ImageGeneratorTest extends TestCase
{
    public function testCanGenerateToFile(): void
    {
        $generator = new ImageGenerator(
            targetSize: "200x200",
            textColorHex: "#333",
            backgroundColorHex: "#AFB",
        );

        $path = __DIR__ . '/output.png';

        $generator->generate(output: $path);

        $this->assertEquals('image/png', mime_content_type($path));

        unlink($path);
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
}