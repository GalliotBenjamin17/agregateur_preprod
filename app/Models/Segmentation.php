<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Segmentation extends Model
{
    use HasSlug, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'chart_spread_years' => 'integer',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'segmentation_id', 'id');
    }

    public function methodFormGroups(): HasMany
    {
        return $this->hasMany(MethodFormGroup::class, 'segmentation_id', 'id');
    }
}
