<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GdprRequest extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'email', 'code', 'type', 'send_at', 'expires_at',
    ];

    protected $casts = [
        'send_at' => 'datetime',
        'expires_at' => 'datetime',
        'code' => 'encrypted',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'email', 'email');
    }
}
