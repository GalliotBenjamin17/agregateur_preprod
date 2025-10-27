<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DonationSplit extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'tonne_co2' => 'float',
        'amount' => 'float',
    ];

    public function splitBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'split_by');
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }

    public function donation(): HasOne
    {
        return $this->hasOne(Donation::class, 'id', 'donation_id');
    }

    public function projectCarbonPrice(): HasOne
    {
        return $this->hasOne(ProjectCarbonPrice::class, 'id', 'project_carbon_price_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DonationSplit::class, 'donation_split_id');
    }

    public function childrenSplits(): HasMany
    {
        return $this->hasMany(DonationSplit::class, 'donation_split_id', 'id');
    }

    public function scopeOnlyParents(Builder $query): void
    {
        $query->whereNull('donation_split_id');
    }
}
