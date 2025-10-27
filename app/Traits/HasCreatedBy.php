<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasCreatedBy
{
    /**
     * Relation for the createdBy User model.
     */
    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
