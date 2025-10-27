<?php

namespace App\Models;

use App\Enums\Models\News\NewsStateEnum;
use App\Traits\HasCreatedBy;
use App\Traits\HasSlug;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    use HasCreatedBy, HasSlug, HasTenant, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'state' => NewsStateEnum::class,
        'scheduled_at' => 'datetime',
        'has_notification' => 'boolean',
        'notified_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
