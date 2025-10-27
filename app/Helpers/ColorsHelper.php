<?php

namespace App\Helpers;

class ColorsHelper
{
    /**
     * @return string
     */
    public static function getTextColour($hex)
    {
        [$red, $green, $blue] = sscanf($hex, '#%02x%02x%02x');
        $luma = ($red + $green + $blue) / 3;

        if ($luma < 154) {
            $textcolour = 'white';
        } else {
            $textcolour = '#404040';
        }

        return $textcolour;
    }
}
