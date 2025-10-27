<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

class Reminder extends Model
{
    use HasTenant, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'reminder_at' => 'date',
        'notification_at' => 'date',
    ];

    public function scopeNotificationToday($q)
    {
        return $q->whereDate('notification_at', Carbon::today());
    }

    public function scopeNotificationTomorrow($q)
    {
        return $q->whereDate('notification_at', Carbon::tomorrow());
    }

    public function related(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'related_type', 'related_id');
    }

    public function createdBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class, 'id', 'tenant_id');
    }
}
