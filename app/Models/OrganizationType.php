<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationType extends Model
{
    use HasSlug, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'organization_type_id');
    }

    public function organizationTypeLinks(): HasMany
    {
        return $this->hasMany(OrganizationTypeLink::class, 'organization_type_id');
    }
}
