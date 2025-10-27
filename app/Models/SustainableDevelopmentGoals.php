<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SustainableDevelopmentGoals extends Model
{
    use HasSlug, HasUuids;

    public $incrementing = false;

    protected $guarded = ['id'];
}
