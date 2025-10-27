<?php

namespace App\Models;

use App\Helpers\DonationHelper;
use App\Traits\HasCreatedBy;
use App\Traits\HasFiles;
use App\Traits\HasRedirection;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Donation extends Model
{
    use HasCreatedBy, HasFiles, HasRedirection, HasTenant, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'source_informations' => 'array',
        'transaction_informations' => 'array',
        'is_donation_splits_full' => 'bool',
        'certificate_pdf_generated_at' => 'datetime',
        'amount' => 'float',
        'redirected_at' => 'datetime',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'related_type', 'related_id');
    }

    public function donationSplits(): HasMany
    {
        return $this->hasMany(DonationSplit::class, 'donation_id', 'id');
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'donation_id', 'id');
    }

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class, 'id', 'tenant_id');
    }

    // Misc
    public function isSplitsFull()
    {
        return $this->donationSplits()->onlyParents()->sum('amount') >= $this->amount;
    }

    public function getAvailableAmount(): float
    {
        return $this->amount - $this->donationSplits()->onlyParents()->sum('amount');
    }

    public function generateCertificate(): string
    {
        $path = DonationHelper::generateCertificate($this);

        return $path;
    }
}
