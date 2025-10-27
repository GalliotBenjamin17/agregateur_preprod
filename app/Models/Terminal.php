<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Terminal extends Model
{
    use HasSlug, HasTenant, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class, 'id', 'tenant_id');
    }
}
