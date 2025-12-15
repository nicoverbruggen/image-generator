<?php

namespace NicoVerbruggen\ImageGenerator;

class RequestHandler
{
    private string $size = '200x200';
    private string $textColor = '#333';
    private string $backgroundColor = '#EEE';

    public function __construct(array $params = [])
    {
        $this->parseParams($params);
    }

    private function parseParams(array $params): void
    {
        if (array_key_exists('size', $params)) {
            $this->size = $params['size'];
        }

        if (array_key_exists('text_color', $params)) {
            $sanitized = $this->sanitizeHexColor($params['text_color']);
            if ($sanitized !== '') {
                $this->textColor = '#' . $sanitized;
            }
        }

        if (array_key_exists('background_color', $params)) {
            $sanitized = $this->sanitizeHexColor($params['background_color']);
            if ($sanitized !== '') {
                $this->backgroundColor = '#' . $sanitized;
            }
        }
    }

    private function sanitizeHexColor(string $color): string
    {
        return preg_replace('/[^a-fA-F0-9]/', '', $color);
    }

    public function getSize(): string
    {
        return $this->size;
    }

    public function getTextColor(): string
    {
        return $this->textColor;
    }

    public function getBackgroundColor(): string
    {
        return $this->backgroundColor;
    }

    public function createGenerator(): ImageGenerator
    {
        return new ImageGenerator(
            targetSize: $this->size,
            textColorHex: $this->textColor,
            backgroundColorHex: $this->backgroundColor,
        );
    }
}
