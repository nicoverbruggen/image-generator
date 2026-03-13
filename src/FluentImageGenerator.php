<?php

namespace NicoVerbruggen\ImageGenerator;

class FluentImageGenerator
{
    private string $text;
    private ?int $width = null;
    private ?int $height = null;
    private ?string $backgroundColor = null;
    private ?string $foregroundColor = null;
    private ?string $fontPath = null;
    private ?int $fontSize = null;
    private ?float $lineHeight = null;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function width(int $width): self
    {
        $this->width = $width;
        return $this;
    }

    public function height(int $height): self
    {
        $this->height = $height;
        return $this;
    }

    public function size(int $width, int $height): self
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    public function backgroundColor(string $hex): self
    {
        $this->backgroundColor = $hex;
        return $this;
    }

    public function foregroundColor(string $hex): self
    {
        $this->foregroundColor = $hex;
        return $this;
    }

    public function font(string $path, int $size = 12): self
    {
        $this->fontPath = $path;
        $this->fontSize = $size;
        return $this;
    }

    public function lineHeight(float $lineHeight): self
    {
        $this->lineHeight = $lineHeight;
        return $this;
    }

    public function toBase64(): string
    {
        return $this->buildGenerator()->generate(
            text: $this->text,
            output: 'base64'
        );
    }

    public function toPng(string $path): bool
    {
        return $this->buildGenerator()->generate(
            text: $this->text,
            output: $path
        );
    }

    private function buildGenerator(): ImageGenerator
    {
        $width = $this->width ?? 200;
        $height = $this->height ?? $width;

        return new ImageGenerator(
            targetSize: "{$width}x{$height}",
            textColorHex: $this->foregroundColor,
            backgroundColorHex: $this->backgroundColor,
            fontPath: $this->fontPath,
            fontSize: $this->fontSize ?? 12,
            lineHeight: $this->lineHeight ?? 1.4,
        );
    }
}
