<?php

namespace App\Models;

use App\Traits\HasCreatedBy;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MethodForm extends Model
{
    use HasCreatedBy, HasSlug, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    protected $casts = [
        'skeleton' => 'array',
        'locked_at' => 'datetime',
    ];

    public function methodFormGroup(): HasOne
    {
        return $this->hasOne(MethodFormGroup::class, 'id', 'method_form_group_id');
    }

    public function lockedBy(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'locked_by');
    }
}
