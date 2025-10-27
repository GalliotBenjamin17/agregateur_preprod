<?php

namespace App\Traits;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasDonations
{
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'related_id', 'id')
            ->where('related_type', get_class($this));
    }
}
