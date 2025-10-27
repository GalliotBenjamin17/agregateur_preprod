<?php

namespace App\Models;

use App\Traits\HasCreatedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectCarbonPrice extends Model
{
    use HasCreatedBy, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'sync_with_tenant' => 'boolean',
        'price' => 'float',
    ];

    public function project()
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }

    public function donationsSplit(): HasMany
    {
        return $this->hasMany(DonationSplit::class, 'project_carbon_price_id', 'id');
    }
}
