<?php

use PHPUnit\Framework\TestCase;
use NicoVerbruggen\ImageGenerator\RequestHandler;
use NicoVerbruggen\ImageGenerator\ImageGenerator;

class RequestHandlerTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $handler = new RequestHandler([]);

        $this->assertEquals('200x200', $handler->getSize());
        $this->assertEquals('#333', $handler->getTextColor());
        $this->assertEquals('#EEE', $handler->getBackgroundColor());
    }

    public function testParsesSize(): void
    {
        $handler = new RequestHandler(['size' => '400x300']);

        $this->assertEquals('400x300', $handler->getSize());
    }

    public function testParsesTextColor(): void
    {
        $handler = new RequestHandler(['text_color' => 'FF0000']);

        $this->assertEquals('#FF0000', $handler->getTextColor());
    }

    public function testParsesTextColorWithHash(): void
    {
        $handler = new RequestHandler(['text_color' => '#00FF00']);

        $this->assertEquals('#00FF00', $handler->getTextColor());
    }

    public function testParsesBackgroundColor(): void
    {
        $handler = new RequestHandler(['background_color' => 'AABBCC']);

        $this->assertEquals('#AABBCC', $handler->getBackgroundColor());
    }

    public function testParsesBackgroundColorWithHash(): void
    {
        $handler = new RequestHandler(['background_color' => '#123456']);

        $this->assertEquals('#123456', $handler->getBackgroundColor());
    }

    public function testSanitizesTextColorRemovesInvalidChars(): void
    {
        $handler = new RequestHandler(['text_color' => 'FFXX00']);

        $this->assertEquals('#FF00', $handler->getTextColor());
    }

    public function testSanitizesBackgroundColorRemovesInvalidChars(): void
    {
        $handler = new RequestHandler(['background_color' => '00GG00']);

        $this->assertEquals('#0000', $handler->getBackgroundColor());
    }

    public function testEmptyColorAfterSanitizationUsesDefault(): void
    {
        $handler = new RequestHandler(['text_color' => '!!!']);

        $this->assertEquals('#333', $handler->getTextColor());
    }

    public function testEmptyBackgroundColorAfterSanitizationUsesDefault(): void
    {
        $handler = new RequestHandler(['background_color' => '<>']);

        $this->assertEquals('#EEE', $handler->getBackgroundColor());
    }

    public function testParsesAllParamsTogether(): void
    {
        $handler = new RequestHandler([
            'size' => '800x600',
            'text_color' => 'FFF',
            'background_color' => '000',
        ]);

        $this->assertEquals('800x600', $handler->getSize());
        $this->assertEquals('#FFF', $handler->getTextColor());
        $this->assertEquals('#000', $handler->getBackgroundColor());
    }

    public function testCreateGeneratorReturnsImageGenerator(): void
    {
        $handler = new RequestHandler([]);

        $generator = $handler->createGenerator();

        $this->assertInstanceOf(ImageGenerator::class, $generator);
    }

    public function testCreateGeneratorUsesCorrectValues(): void
    {
        $handler = new RequestHandler([
            'size' => '300x200',
            'text_color' => 'ABC',
            'background_color' => 'DEF',
        ]);

        $generator = $handler->createGenerator();

        $this->assertEquals('300x200', $generator->targetSize);
        $this->assertEquals('#ABC', $generator->textColorHex);
        $this->assertEquals('#DEF', $generator->backgroundColorHex);
    }

    public function testIgnoresUnknownParams(): void
    {
        $handler = new RequestHandler([
            'size' => '100x100',
            'unknown_param' => 'value',
            'another' => 'test',
        ]);

        $this->assertEquals('100x100', $handler->getSize());
        $this->assertEquals('#333', $handler->getTextColor());
        $this->assertEquals('#EEE', $handler->getBackgroundColor());
    }

    public function testLowercaseHexIsPreserved(): void
    {
        $handler = new RequestHandler(['text_color' => 'aabbcc']);

        $this->assertEquals('#aabbcc', $handler->getTextColor());
    }

    public function testMixedCaseHexIsPreserved(): void
    {
        $handler = new RequestHandler(['background_color' => 'AaBbCc']);

        $this->assertEquals('#AaBbCc', $handler->getBackgroundColor());
    }
}
