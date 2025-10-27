<?php

namespace App\Models;

use App\Traits\HasCreatedBy;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use HasCreatedBy, HasTenant, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'shopping_cart' => 'array',
        'channel_options' => 'array',
        'expiration_at' => 'datetime',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'related_type', 'related_id');
    }

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class, 'id', 'tenant_id');
    }
}
