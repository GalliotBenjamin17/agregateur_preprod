<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTablePreference extends Model
{
    protected $fillable = [
        'user_id',
        'table_key',
        'toggled_columns',
        'saved_filters',
    ];

    protected $casts = [
        'toggled_columns' => 'array',
        'saved_filters' => 'array',
    ];
}
