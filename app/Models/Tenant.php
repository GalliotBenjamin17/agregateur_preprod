<?php

namespace App\Models;

use App\Traits\HasAddress;
use App\Traits\HasFiles;
use App\Traits\HasSlug;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class Tenant extends Model
{
    use HasAddress, HasFactory, HasFiles, HasSlug, HasTenant, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'contributor_space_banner_activated' => 'boolean',
        'payments_mode_test' => 'boolean',
        'faq' => 'array',
        'documents_communication' => 'array',
        'external_resources' => 'array',
        'cgv_updated_at' => 'date',
    ];

    public function address(): Attribute
    {
        return Attribute::get(function () {
            return Arr::join(collect([$this->address_1, $this->postal_code, $this->city])->whereNotNull()->toArray(), ', ', ', ');
        });
    }

    public function newQuery()
    {
        return parent::newQuery()->when(userHasTenant(), function ($query) {
            return $query->where('id', userTenantId());
        });
    }

    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function organization(): HasOne
    {
        return $this->hasOne(Organization::class, 'id', 'default_organization_id');
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'tenant_id', 'id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_id', 'id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'tenant_id', 'id');
    }

    // Misc
    public function syncNewsWithWebhook(): bool
    {
        if (is_null($this->webhook_news_update)) {
            return false;
        }

        $response = Http::get($this->webhook_news_update, [
            'update' => true,
        ]);

        return true;
    }

    public function syncProjectsWithWebhook(): bool
    {
        if (is_null($this->webhook_project_update)) {
            return false;
        }

        $response = Http::get($this->webhook_project_update, [
            'update' => true,
        ]);

        return true;
    }

    public function syncUsersWithWebhook(): bool
    {
        if (is_null($this->webhook_users_update)) {
            return false;
        }

        $response = Http::get($this->webhook_users_update, [
            'update' => true,
        ]);

        return true;
    }
}
