<?php

namespace App\Models;

use App\Traits\HasRedirection;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    use HasRedirection, HasSlug, HasUuids;

    protected $guarded = ['id', 'slug'];

    public function scopeOnModel($q, Model $model)
    {
        return $q->where('related_id', $model->id)->where('related_type', get_class($model));
    }

    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function related(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'related_type', 'related_id');
    }
}
