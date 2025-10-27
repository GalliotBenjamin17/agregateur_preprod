<?php

namespace App\Traits;

trait HasTenant
{
    public function newQuery()
    {
        return parent::newQuery()->when(userHasTenant(), function ($query) {
            return $query->where('tenant_id', userTenantId());
        });
    }
}
