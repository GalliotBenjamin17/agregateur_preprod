<?php

namespace App\Models;

use App\Traits\HasCreatedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectHolderPayment extends Model
{
    use HasCreatedBy, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'amount_ht' => 'float',
        'amount' => 'float',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
