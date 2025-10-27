<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Certification extends Model
{
    use HasSlug, HasUuids;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Support\HigherOrderWhenProxy
     */
    public function newQuery()
    {
        return parent::newQuery()->when(userHasTenant(), function ($query) {
            return $query->where('tenant_id', userTenantId())->orWhereNull('tenant_id');
        });
    }

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class, 'id', 'tenant_id');
    }
}
