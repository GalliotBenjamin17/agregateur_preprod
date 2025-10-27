<?php

namespace App\Providers;

use Filament\Forms\Components\FileUpload;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::disk('local')->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
            return URL::temporarySignedRoute(
                'local.temp',
                $expiration,
                array_merge($options, ['path' => $path])
            );
        });

        Request::macro('subdomain', function () {
            if ($this->getHttpHost() == config('app.displayed_url')) {
                return null;
            }

            return current(explode('.', $this->getHttpHost()));
        });

        FileUpload::macro('genericUpload', function (string $path) {
            $this->disk('s3')
                ->visibility('public')
                ->preserveFilenames()
                ->saveUploadedFileUsing(function ($file) use ($path) {
                    return storeToS3($file, $path);
                })
                ->downloadable()
                ->openable()
                ->reorderable();

            return $this;
        });

        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'secondary' => Color::Gray,
            'primary' => Color::Blue,
            'success' => Color::Green,
            'warning' => Color::Amber,
        ]);

        require_once app_path('helpers.php');
    }
}
