<?php

namespace NicoVerbruggen\ImageGenerator\Helpers;

class ColorHelper
{
    /**
     * Generates a random hex color.
     *
     * Taken from https://stackoverflow.com/a/9901154.
     *
     * @return string
     */
    public static function randomHex(): string
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Uses the luminosity algorithm to determine the hex contrast color
     * for a given background color.
     *
     * Taken from https://stackoverflow.com/a/42921358.
     *
     * @return string
     */
    public static function contrastColor(?string $hex): string
    {
        if ($hex === null || $hex === '') {
            $hex = "#000000";
        }

        // Remove the # to avoid errors
        $hex = str_replace('#', '', $hex);

        // Expand 3-char hex to 6-char (e.g., "FFF" -> "FFFFFF")
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        // hexColor RGB
        $R1 = hexdec(substr($hex, 0, 2));
        $G1 = hexdec(substr($hex, 2, 2));
        $B1 = hexdec(substr($hex, 4, 2));

        // Black RGB
        $blackColor = "000000";
        $R2BlackColor = hexdec(substr($blackColor, 0, 2));
        $G2BlackColor = hexdec(substr($blackColor, 2, 2));
        $B2BlackColor = hexdec(substr($blackColor, 4, 2));

        // Calc contrast ratio
        $L1 = 0.2126 * pow($R1 / 255, 2.2) +
            0.7152 * pow($G1 / 255, 2.2) +
            0.0722 * pow($B1 / 255, 2.2);

        $L2 = 0.2126 * pow($R2BlackColor / 255, 2.2) +
            0.7152 * pow($G2BlackColor / 255, 2.2) +
            0.0722 * pow($B2BlackColor / 255, 2.2);

        if ($L1 > $L2) {
            $contrastRatio = (int)(($L1 + 0.05) / ($L2 + 0.05));
        } else {
            $contrastRatio = (int)(($L2 + 0.05) / ($L1 + 0.05));
        }

        // If contrast is more than 5, return black color
        if ($contrastRatio > 5) {
            return '#000';
        } else { // if not, return white color.
            return '#FFF';
        }
    }
}
