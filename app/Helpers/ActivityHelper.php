<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityHelper
{
    public static function push(
        Model $performedOn,
        string $title,
        ?User $causedBy = null,
        ?string $description = null,
        ?string $url = null,
    ): void {
        is_null($causedBy) ? $causedBy = request()->user() : null;

        if ($description) {
            $properties['description'] = $description;
        }
        if ($url) {
            $properties['url'] = $url;
        }

        activity()
            ->performedOn($performedOn)
            ->causedBy($causedBy)
            ->withProperties($properties ?? [])
            ->log($title);
    }
}
