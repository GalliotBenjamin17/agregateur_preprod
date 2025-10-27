<?php

namespace App\Models;

use App\Traits\HasAddress;
use App\Traits\HasDonations;
use App\Traits\HasRedirection;
use App\Traits\HasSlug;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;
use Soved\Laravel\Gdpr\Portable;

class Organization extends Model
{
    use HasAddress, HasDonations, HasFactory, HasRedirection, HasSlug, HasTenant, HasUuids, Portable, Searchable;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'contacts' => 'array',
        'legal_created_at' => 'date',
        'legal_is_ess' => 'boolean',
        'is_shareholder' => 'boolean',
    ];

    protected $gdprWith = [
        'files',
    ];

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
        ];
    }

    public function organizationType(): BelongsTo
    {
        return $this->belongsTo(OrganizationType::class);
    }

    public function organizationTypeLinks(): HasMany
    {
        return $this->hasMany(OrganizationTypeLink::class, 'organization_type_id', 'organization_type_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'related_id', 'id')
            ->where('related_type', get_class($this));
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_organizations');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function donationSplits(): HasManyThrough
    {
        return $this->hasManyThrough(DonationSplit::class, Donation::class, 'related_id');
    }

    public function parentOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_parent_id', 'id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'sponsor_id', 'id')
            ->where('sponsor_type', get_class($this));
    }

    public function manager(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'manager_id');
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'badge_organizations');
    }
}
