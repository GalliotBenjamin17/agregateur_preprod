<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    use HasSlug, HasUuids;
    use HasTenant;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'badge_organizations');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActivated($query)
    {
        return $query->whereNotNull('activated_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Functions
    |--------------------------------------------------------------------------
    */

    public function isActivated(): bool
    {
        return (bool) $this->activated_at;
    }
}
