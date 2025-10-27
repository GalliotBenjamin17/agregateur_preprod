<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrganizationTypeLink extends Model
{
    use HasSlug, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    public function organizationType(): HasOne
    {
        return $this->hasOne(OrganizationType::class, 'id', 'organization_type_id');
    }
}
