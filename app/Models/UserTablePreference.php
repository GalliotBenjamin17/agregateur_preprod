<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTablePreference extends Model
{
    protected $fillable = [
        'user_id',
        'table_key',
        'toggled_columns',
    ];

    protected $casts = [
        'toggled_columns' => 'array',
        'saved_filters' => 'array',
    ];
}
