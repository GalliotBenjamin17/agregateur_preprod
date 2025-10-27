<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Unsubscribe extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key', 'value', 'request_at', 'request_why',
    ];

    protected $casts = [
        'request_at' => 'datetime',
    ];
}
