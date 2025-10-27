<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        Carbon::macro('userDatetimeFormat', function (?Carbon $date, string $format = 'H\hi, \l\e d/m/y', string $otherwise = '-'): string {
            return $date === null ? $otherwise : $date->format($format);
        });

        Carbon::macro('userDatetime', function (?Carbon $date, bool $hours = true, bool $seconds = false, string $otherwise = '-', bool $capitalized = false): string {
            if ($date === null) {
                return $otherwise;
            }

            $datetime = $date->timezone('Europe/Paris');

            if ($date->isBefore(Carbon::yesterday())) {
                $fmt = $date->isBefore(Carbon::now()->startOfYear()) ? 'd/m/y' : 'd/m';
                $res = "le {$datetime->format($fmt)}";
            } elseif ($date->isAfter(Carbon::today()->endOfDay())) {
                $fmt = $date->isAfter(Carbon::now()->endOfYear()) ? 'd/m/y' : 'd/m';
                $res = "le {$datetime->format($fmt)}";
            } elseif ($date->isBefore(Carbon::today()->startOfDay())) {
                $res = 'hier';
            } else {
                $res = "aujourd'hui";
            }

            $fmt = $seconds ? 'H\hi:s' : 'H\hi';
            $res = $res.($hours ? " à {$datetime->format($fmt)}" : '');

            return $capitalized ? ucwords($res) : $res;
        });

        Blade::directive('datetime', fn ($expression) => "<?php echo \Carbon\Carbon::userDatetime(".$expression.') ?>');
        Blade::directive('dateformat', fn ($expression) => "<?php echo \Carbon\Carbon::userDatetimeFormat(".$expression.') ?>');
    }
}
