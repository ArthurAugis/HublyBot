<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'slug', 'price_1', 'price_12', 'price_24', 'features'])]
class Plan extends Model
{
    protected $casts = [
        'features' => 'array',
    ];
}
