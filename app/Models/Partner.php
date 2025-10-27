<?php

namespace App\Models;

use App\Traits\HasCreatedBy;
use App\Traits\HasFiles;
use App\Traits\HasSlug;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    use HasCreatedBy, HasFiles, HasSlug, HasTenant, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'contacts' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function partnerProjects(): HasMany
    {
        return $this->hasMany(PartnerProject::class, 'partner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'partner_users');
    }
}
