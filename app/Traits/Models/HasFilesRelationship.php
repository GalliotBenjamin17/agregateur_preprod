<?php

namespace App\Traits\Models;

use App\Models\File;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasFilesRelationship
{
    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'related_id', 'id')
            ->where('related_type', get_class($this));
    }
}
