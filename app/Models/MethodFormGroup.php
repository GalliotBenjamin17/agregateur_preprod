<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MethodFormGroup extends Model
{
    use HasSlug, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];

    public function activeMethodForm(): HasOne
    {
        return $this->hasOne(MethodForm::class, 'id', 'active_method_form_id');
    }

    public function methodForms(): HasMany
    {
        return $this->hasMany(MethodForm::class, 'method_form_group_id', 'id');
    }

    public function segmentation(): HasOne
    {
        return $this->hasOne(Segmentation::class, 'id', 'segmentation_id');
    }
}
