<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasFiles
{
    /**
     * Relation for the file model.
     *
     * @return void
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'related_id', 'id')
            ->where('related_type', get_class($this));
    }
}
