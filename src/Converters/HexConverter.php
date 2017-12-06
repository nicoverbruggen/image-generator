<?php

namespace NicoVerbruggen\ImageGenerator\Converters;

class HexConverter
{
    /**
     * Allocates a specific hex color for gd.
     * You must have allocated the resource beforehand.
     * Returns an int representing the desired color.
     *
     * @param $resource
     * @param $hex
     * @return int
     */
    public static function allocate($resource, $hex)
    {
        $rgbArray = self::toRgbArray($hex);
        return imagecolorallocate($resource, $rgbArray['r'], $rgbArray['g'], $rgbArray['b']);
    }

    /**
     * Converts a hex color string to an array (r: int, g: int, b: int).
     * This array can then be used if you need rgb values.
     *
     * @param $hex
     * @return array|bool
     */
    public static function toRgbArray($hex)
    {
        $hex = preg_replace("/[^abcdef0-9]/i", "", $hex);
        if (strlen($hex) == 6) {
            list($r, $g, $b) = str_split($hex, 2);
            return [
                "r" => hexdec($r),
                "g" => hexdec($g),
                "b" => hexdec($b)
            ];
        } elseif (strlen($hex) == 3) {
            list($r, $g, $b) = array($hex[0] . $hex[0], $hex[1] . $hex[1], $hex[2] . $hex[2]);
            return [
                "r" => hexdec($r),
                "g" => hexdec($g),
                "b" => hexdec($b)
            ];
        }
        return false;
    }
}