<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Generate a unique Slug for the model.
     */
    public static function bootHasSlug(): void
    {
        static::creating(function (self $model) {
            $model->slug = $model->slug ?: Str::of($model->name)->append('-'.Str::random(6))->slug()->lower();
        });
    }
}
