<?php

namespace App\Models;

use App\Enums\Models\Partners\CommissionTypeEnum;
use App\Traits\HasCreatedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerProject extends Model
{
    use HasCreatedBy, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'commission_type' => CommissionTypeEnum::class,
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PartnerProjectPayment::class, 'partner_project_id');
    }
}
