<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Bot extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'name',
        'token',
        'settings',
        'status',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
