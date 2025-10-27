<?php

namespace App\Helpers;

class TVAHelper
{
    const TVA_PERCENTAGE = 0.20;

    const TVA_PERCENTAGE_100 = 20;

    /*
     * TTC amount
     */
    public static function getHT(?float $amount): float
    {
        if (! $amount) {
            return 0;
        }

        return round((100 * $amount) / (100 + self::TVA_PERCENTAGE_100), 2);
    }

    /*
     * HT amount
     */
    public static function getTTC(float $amount): float
    {
        return round($amount + ($amount * self::TVA_PERCENTAGE_100 / 100), 2);
    }
}
