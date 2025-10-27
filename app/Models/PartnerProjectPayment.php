<?php

namespace App\Models;

use App\Enums\Models\PartnerProjectPayments\PaymentStateEnum;
use App\Traits\HasCreatedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerProjectPayment extends Model
{
    use HasCreatedBy, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'payment_state' => PaymentStateEnum::class,
    ];

    public function partnerProject(): BelongsTo
    {
        return $this->belongsTo(PartnerProject::class);
    }
}
